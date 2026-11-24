<?php

namespace Tests\Feature\Database;

use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase3SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_phase_3_tables_exist(): void
    {
        $schema = app(Builder::class);

        $expected = [
            'roles',
            'permissions',
            'products',
            'categories',
            'product_images',
            'product_variants',
            'inventory',
            'carts',
            'cart_items',
            'addresses',
            'coupons',
            'orders',
            'order_items',
            'payments',
            'refunds',
            'shipments',
            'devices',
            'device_models',
            'device_activations',
            'device_sensor_data',
            'device_events',
            'emergency_contacts',
            'emergency_events',
            'emergency_notifications',
            'locations',
            'subscriptions',
            'subscription_plans',
            'notifications',
            'audit_logs',
        ];

        foreach ($expected as $table) {
            $this->assertTrue($schema->hasTable($table), "Table {$table} is missing.");
        }
    }
}
