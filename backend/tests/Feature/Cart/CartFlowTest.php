<?php

namespace Tests\Feature\Cart;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_can_add_items_and_calculate_totals(): void
    {
        $product = Product::create([
            'name' => 'Air Purifier Mini',
            'slug' => 'air-purifier-mini',
            'sku' => 'APM-001',
            'price' => 99.00,
            'status' => 'published',
            'type' => 'physical',
            'is_active' => true,
        ]);

        $response = $this->withHeader('X-Cart-Session', 'guest-cart-123')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.items.0.product_id', $product->id)
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.summary.subtotal', 198.00)
            ->assertJsonPath('data.summary.total', 213.84);
    }

    public function test_user_cart_merge_and_coupon_application_work(): void
    {
        $role = Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'description' => 'Customer',
            'is_system' => true,
        ]);

        $user = User::factory()->create(['role_id' => $role->id]);

        $product = Product::create([
            'name' => 'Smart Sensor',
            'slug' => 'smart-sensor',
            'sku' => 'SS-001',
            'price' => 120.00,
            'status' => 'published',
            'type' => 'physical',
            'is_active' => true,
        ]);

        Coupon::create([
            'code' => 'SAVE10',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 50,
            'status' => 'active',
        ]);

        $this->withHeader('X-Cart-Session', 'guest-cart-merge')
            ->postJson('/api/v1/cart/items', [
                'product_id' => $product->id,
                'quantity' => 1,
            ]);

        $loginResponse = $this->actingAs($user, 'sanctum')
            ->withHeader('X-Cart-Session', 'guest-cart-merge')
            ->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $loginResponse->assertStatus(200);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/cart/coupon', ['code' => 'SAVE10'])
            ->assertStatus(200)
            ->assertJsonPath('data.coupon.code', 'SAVE10')
            ->assertJsonPath('data.summary.discount_amount', 12.00);

        $this->actingAs($user, 'sanctum')->deleteJson('/api/v1/cart/items/' . $product->id)->assertStatus(200);
    }
}
