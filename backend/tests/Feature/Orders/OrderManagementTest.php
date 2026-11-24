<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_own_orders_and_cannot_view_another_users_order(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $product = Product::create([
            'name' => 'Order Product',
            'slug' => 'order-product',
            'sku' => 'OP-001',
            'price' => 149.99,
            'status' => 'published',
            'type' => 'physical',
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-2026-000001',
            'status' => OrderStatus::PENDING->value,
            'subtotal' => 149.99,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 149.99,
            'currency' => 'INR',
            'payment_status' => 'pending',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 149.99,
            'total_price' => 149.99,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/orders')
            ->assertStatus(200)
            ->assertJsonPath('data.0.user_id', $user->id);

        $this->actingAs($other, 'sanctum')
            ->getJson('/api/v1/orders/' . $order->id)
            ->assertStatus(403);
    }

    public function test_invalid_status_transition_is_rejected(): void
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'order_number' => 'ORD-2026-000002',
            'status' => OrderStatus::DELIVERED->value,
            'subtotal' => 10,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 10,
            'currency' => 'INR',
            'payment_status' => 'paid',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        app(\App\Services\OrderStatusTransitionService::class)->transition($order, OrderStatus::PROCESSING);
    }
}
