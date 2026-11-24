<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_product_catalog_and_detail_endpoints_work(): void
    {
        $category = Category::create([
            'name' => 'Home Security',
            'slug' => 'home-security',
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Smart Lock Pro',
            'slug' => 'smart-lock-pro',
            'sku' => 'SLP-001',
            'price' => 149.99,
            'description' => 'A connected smart lock for home security.',
            'status' => 'published',
            'type' => 'physical',
            'is_featured' => true,
            'is_active' => true,
            'stock_quantity' => 24,
        ]);

        $list = $this->getJson('/api/v1/products?search=lock&per_page=10');
        $list->assertStatus(200)
            ->assertJsonPath('data.0.name', 'Smart Lock Pro')
            ->assertJsonPath('meta.total', 1);

        $detail = $this->getJson('/api/v1/products/smart-lock-pro');
        $detail->assertStatus(200)
            ->assertJsonPath('data.slug', 'smart-lock-pro')
            ->assertJsonPath('data.name', 'Smart Lock Pro');
    }

    public function test_admin_can_manage_products_but_customers_cannot(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Administrator',
            'is_system' => true,
        ]);

        $customerRole = Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'description' => 'Customer',
            'is_system' => true,
        ]);

        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $customer = User::factory()->create(['role_id' => $customerRole->id]);

        $category = Category::create([
            'name' => 'Smart Devices',
            'slug' => 'smart-devices',
            'is_active' => true,
        ]);

        $createPayload = [
            'category_id' => $category->id,
            'name' => 'Motion Sensor Plus',
            'slug' => 'motion-sensor-plus',
            'sku' => 'MSP-101',
            'price' => 89.50,
            'description' => 'Advanced motion sensor.',
            'status' => 'published',
            'type' => 'physical',
            'stock_quantity' => 12,
            'is_featured' => true,
            'is_active' => true,
        ];

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/v1/products', $createPayload)
            ->assertStatus(403);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/products', $createPayload)
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Motion Sensor Plus')
            ->assertJsonPath('data.slug', 'motion-sensor-plus');

        $this->assertDatabaseHas('products', ['slug' => 'motion-sensor-plus']);
    }
}
