<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderStatusTransitionService $transitionService,
    ) {}

    public function listForUser(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $request->user()->orders()->with(['items', 'shipment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        return $query->paginate((int) $request->input('per_page', 15));
    }

    public function findForUser(Request $request, string $id): Order
    {
        $order = $request->user()->orders()->with(['items', 'shipment', 'shippingAddress', 'billingAddress', 'payments'])->find($id);

        if (! $order) {
            abort(403, 'You do not have access to this order.');
        }

        return $order;
    }

    public function createFromCheckout(Request $request): Order
    {
        $payment = Payment::query()->where('transaction_reference', $request->input('payment_reference'))->first();

        if (! $payment) {
            throw ValidationException::withMessages(['payment' => ['Payment record not found.']]);
        }

        if ($payment->status !== 'paid') {
            throw ValidationException::withMessages(['payment' => ['Payment must be verified before order creation.']]);
        }

        $order = Order::query()->where('payment_status', 'paid')->where('status', '!=', OrderStatus::CANCELLED->value)->where('notes', 'like', '%payment_reference%')->first();
        if ($order && $order->payments()->where('transaction_reference', $request->input('payment_reference'))->exists()) {
            throw ValidationException::withMessages(['payment' => ['This payment has already created an order.']]);
        }

        $cart = $this->resolveCart($request);
        if (! $cart || $cart->items()->count() === 0) {
            throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
        }

        $checkoutSummary = app(\App\Services\CheckoutService::class)->getCheckoutSummary($request);

        return DB::transaction(function () use ($request, $cart, $checkoutSummary, $payment) {
            $user = $request->user();
            $orderNumber = $this->generateOrderNumber();

            $order = Order::create([
                'user_id' => $user?->id,
                'order_number' => $orderNumber,
                'status' => OrderStatus::PAID->value,
                'subtotal' => round((float) $checkoutSummary['subtotal'], 2),
                'discount_amount' => round((float) ($checkoutSummary['discount'] ?? 0), 2),
                'shipping_amount' => round((float) ($checkoutSummary['shipping'] ?? 0), 2),
                'tax_amount' => round((float) ($checkoutSummary['tax'] ?? 0), 2),
                'total_amount' => round((float) $checkoutSummary['grand_total'], 2),
                'currency' => 'INR',
                'payment_status' => 'paid',
                'notes' => 'Created from verified Razorpay payment',
            ]);

            foreach ($checkoutSummary['items'] ?? [] as $item) {
                $product = Product::query()->find($item['product_id']);

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => null,
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant'] ?? null,
                    'sku' => $product?->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => round((float) $item['unit_price'], 2),
                    'discount_amount' => 0,
                    'tax_amount' => 0,
                    'total_price' => round((float) $item['line_total'], 2),
                ]);

                if ($product) {
                    $available = max(0, (int) ($product->stock_quantity ?? 0));
                    if ($item['quantity'] > $available) {
                        throw ValidationException::withMessages(['inventory' => ['Not enough inventory for ' . $product->name . '.']]);
                    }
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            if (! empty($checkoutSummary['shipping_address'])) {
                $order->shipping_address_snapshot = json_encode($checkoutSummary['shipping_address']);
            }
            if (! empty($checkoutSummary['billing_address'])) {
                $order->billing_address_snapshot = json_encode($checkoutSummary['billing_address']);
            }
            $order->save();

            $payment->order_id = $order->id;
            $payment->save();

            $this->transitionService->transition($order, OrderStatus::PROCESSING);

            $cart->status = 'completed';
            $cart->save();

            return $order->fresh(['items', 'payments']);
        });
    }

    public function cancel(Order $order, Request $request): Order
    {
        if ($request->user()?->id !== $order->user_id) {
            abort(403, 'You do not have permission to cancel this order.');
        }

        $this->transitionService->transition($order, OrderStatus::CANCELLED);

        return $order->fresh();
    }

    public function invoiceForUser(Request $request, Order $order): array
    {
        if ($request->user()?->id !== $order->user_id) {
            abort(403, 'You do not have permission to view this invoice.');
        }

        return [
            'order_number' => $order->order_number,
            'order_date' => $order->created_at?->toDateString(),
            'customer' => [
                'name' => $order->user?->name,
                'email' => $order->user?->email,
            ],
            'shipping_address' => $order->shipping_address_snapshot ? json_decode($order->shipping_address_snapshot, true) : null,
            'billing_address' => $order->billing_address_snapshot ? json_decode($order->billing_address_snapshot, true) : null,
            'items' => $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ]),
            'subtotal' => (float) $order->subtotal,
            'discount' => (float) $order->discount_amount,
            'tax' => (float) $order->tax_amount,
            'shipping' => (float) $order->shipping_amount,
            'total' => (float) $order->total_amount,
            'payment_status' => $order->payment_status,
        ];
    }

    protected function generateOrderNumber(): string
    {
        $date = now()->format('Y');
        $sequence = Order::query()->withTrashed()->whereYear('created_at', now()->year)->count() + 1;
        $sequence = str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
        return 'ORD-' . $date . '-' . $sequence;
    }

    protected function resolveCart(Request $request): ?Cart
    {
        $sessionId = $request->header('X-Cart-Session');
        if ($request->user()) {
            return $request->user()->carts()->where('status', 'active')->first();
        }

        if ($sessionId) {
            return Cart::query()->where('status', 'active')->where('session_id', $sessionId)->first();
        }

        return null;
    }
}
