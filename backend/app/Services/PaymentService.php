<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        protected CheckoutService $checkoutService,
    ) {}

    public function createCheckoutPayment(Request $request): array
    {
        $requestMethod = strtolower((string) ($request->input('payment_method') ?? 'cod'));
        $checkout = $this->checkoutService->getCheckoutSummary($request);
        $cart = $this->resolveCart($request);
        $user = $request->user();

        if (! $cart || $cart->items()->count() === 0) {
            throw ValidationException::withMessages(['cart' => ['Your cart is empty.']]);
        }

        $shippingAddress = $this->resolveAddress($request, 'shipping_address', $user);
        $billingAddress = $this->resolveAddress($request, 'billing_address', $user, $shippingAddress);

        $order = Order::create([
            'user_id' => $user?->id,
            'shipping_address_id' => $shippingAddress['id'] ?? null,
            'billing_address_id' => $billingAddress['id'] ?? null,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'status' => 'pending',
            'subtotal' => (float) $checkout['subtotal'],
            'discount_amount' => (float) ($checkout['discount'] ?? 0),
            'shipping_amount' => (float) ($checkout['shipping'] ?? 0),
            'tax_amount' => (float) ($checkout['tax'] ?? 0),
            'total_amount' => (float) $checkout['grand_total'],
            'currency' => 'INR',
            'payment_status' => 'pending',
            'notes' => $requestMethod === 'razorpay' ? 'Razorpay checkout' : 'Cash on delivery',
        ]);

        foreach ($checkout['items'] ?? [] as $item) {
            $product = Product::query()->find($item['product_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => null,
                'product_name' => $item['product_name'],
                'variant_name' => $item['variant'],
                'quantity' => $item['quantity'],
                'unit_price' => (float) $item['unit_price'],
                'total_price' => (float) $item['line_total'],
            ]);

            if ($product) {
                $product->decrement('stock_quantity', $item['quantity']);
            }
        }

        if ($requestMethod !== 'razorpay') {
            return [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => (float) $order->total_amount,
                'currency' => $order->currency,
                'payment_method' => $requestMethod,
                'requires_payment' => false,
            ];
        }

        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');

        if (! $keyId || ! $keySecret) {
            throw ValidationException::withMessages([
                'payment_method' => ['Razorpay is not configured. Add RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET to your environment.'],
            ]);
        }

        $response = Http::withBasicAuth($keyId, $keySecret)
            ->acceptJson()
            ->asJson()
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => (int) round((float) $order->total_amount * 100),
                'currency' => 'INR',
                'receipt' => $order->order_number,
                'notes' => [
                    'order_id' => $order->id,
                    'user_id' => (string) ($user?->id ?? 'guest'),
                ],
            ]);

        if ($response->failed()) {
            $order->delete();
            throw ValidationException::withMessages([
                'payment_method' => ['Razorpay order creation failed.'],
            ]);
        }

        $body = $response->json();
        Payment::create([
            'order_id' => $order->id,
            'user_id' => $user?->id,
            'provider' => 'razorpay',
            'payment_method' => 'razorpay',
            'transaction_reference' => $body['id'] ?? null,
            'status' => 'pending',
            'amount' => (float) $order->total_amount,
            'currency' => 'INR',
            'gateway_response' => json_encode($body),
        ]);

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_method' => 'razorpay',
            'razorpay_order_id' => $body['id'] ?? null,
            'amount' => (int) round((float) $order->total_amount * 100),
            'currency' => 'INR',
            'key_id' => $keyId,
            'requires_payment' => true,
        ];
    }

    public function verifyPayment(Request $request): array
    {
        $signature = (string) $request->header('X-Razorpay-Signature', '');
        $orderId = (string) $request->input('razorpay_order_id', '');
        $paymentId = (string) $request->input('razorpay_payment_id', '');
        $paymentSignature = (string) $request->input('razorpay_signature', '');

        if ($orderId === '' || $paymentId === '' || $paymentSignature === '') {
            throw ValidationException::withMessages([
                'payment' => ['Missing Razorpay payment verification details.'],
            ]);
        }

        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, config('services.razorpay.key_secret'));
        if (! hash_equals($expected, $paymentSignature)) {
            throw ValidationException::withMessages([
                'payment' => ['Invalid Razorpay signature.'],
            ]);
        }

        $payment = Payment::query()->where('transaction_reference', $orderId)->first();
        if (! $payment) {
            throw ValidationException::withMessages([
                'payment' => ['Payment record not found for this Razorpay order.'],
            ]);
        }

        $payment->status = 'paid';
        $payment->gateway_response = json_encode($request->all());
        $payment->save();

        $payment->order()->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        return [
            'success' => true,
            'order_id' => $payment->order_id,
            'payment_id' => $payment->id,
        ];
    }

    public function handleWebhook(Request $request): array
    {
        $rawBody = $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature', '');
        $webhookSecret = config('services.razorpay.webhook_secret');

        if (! $webhookSecret || ! hash_equals(hash_hmac('sha256', $rawBody, $webhookSecret), $signature)) {
            throw ValidationException::withMessages([
                'signature' => ['Invalid Razorpay signature.'],
            ]);
        }

        $payload = json_decode($rawBody, true);
        $entity = $payload['payload']['payment']['entity'] ?? null;

        if (! $entity) {
            return ['received' => true];
        }

        $payment = Payment::query()->where('transaction_reference', $entity['order_id'] ?? null)->first();
        if (! $payment) {
            return ['received' => true, 'status' => 'ignored'];
        }

        $status = match (($payload['event'] ?? '')) {
            'payment.authorized' => 'authorized',
            'payment.failed' => 'failed',
            default => 'paid',
        };

        $payment->status = $status;
        $payment->gateway_response = $rawBody;
        $payment->save();

        $payment->order()->update([
            'payment_status' => $status === 'failed' ? 'failed' : 'paid',
            'status' => $status === 'failed' ? 'failed' : 'processing',
        ]);

        return [
            'received' => true,
            'status' => $status,
            'order_id' => $payment->order_id,
        ];
    }

    protected function resolveAddress(Request $request, string $type, ?\App\Models\User $user, ?array $fallback = null): array
    {
        $data = $request->input($type, $fallback ?? []);
        if (empty($data)) {
            return [];
        }

        if (isset($data['id']) && ! empty($data['id'])) {
            $query = Address::query();
            if ($user) {
                $query->where('user_id', $user->id);
            }

            return $query->findOrFail($data['id'])->toArray();
        }

        $payload = array_intersect_key($data, array_flip([
            'full_name', 'phone', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country', 'label',
        ]));

        if ($user) {
            $payload['user_id'] = $user->id;
        }

        $payload['label'] = $payload['label'] ?? 'Default';
        $address = Address::query()->create($payload);

        return $address->toArray();
    }

    protected function resolveCart(Request $request): ?Cart
    {
        $user = $request->user();
        $sessionId = $request->header('X-Cart-Session');

        if ($user) {
            return $user->carts()->where('status', 'active')->first();
        }

        if ($sessionId) {
            return Cart::query()->where('status', 'active')->where('session_id', $sessionId)->first();
        }

        return Cart::query()->where('status', 'active')->first();
    }
}
