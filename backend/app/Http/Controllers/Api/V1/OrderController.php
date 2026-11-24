<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $orders = $this->orderService->listForUser($request);

        return ApiResponse::paginated($orders, 'Orders retrieved successfully.');
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $order = $this->orderService->findForUser($request, $id);
        $this->authorize('view', $order);

        return ApiResponse::success([
            'order' => $this->serializeOrder($order),
        ], 'Order retrieved successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $order = $this->orderService->createFromCheckout($request);

        return ApiResponse::success([
            'order' => $this->serializeOrder($order),
        ], 'Order created successfully.', 201);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        $this->authorize('cancel', $order);
        $order = $this->orderService->cancel($order, $request);

        return ApiResponse::success([
            'order' => $this->serializeOrder($order),
        ], 'Order cancelled successfully.');
    }

    public function invoice(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);
        $invoice = $this->orderService->invoiceForUser($request, $order);

        return ApiResponse::success($invoice, 'Invoice generated successfully.');
    }

    protected function serializeOrder(Order $order): array
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'tax_amount' => (float) $order->tax_amount,
            'shipping_amount' => (float) $order->shipping_amount,
            'total_amount' => (float) $order->total_amount,
            'currency' => $order->currency,
            'items' => $order->items()->get()->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
            ])->all(),
            'shipping_address' => $order->shipping_address_snapshot ? json_decode($order->shipping_address_snapshot, true) : null,
            'billing_address' => $order->billing_address_snapshot ? json_decode($order->billing_address_snapshot, true) : null,
            'shipment' => $order->shipment ? [
                'carrier' => $order->shipment->carrier,
                'tracking_number' => $order->shipment->tracking_number,
                'status' => $order->shipment->status,
            ] : null,
            'created_at' => $order->created_at?->toISOString(),
        ];
    }
}
