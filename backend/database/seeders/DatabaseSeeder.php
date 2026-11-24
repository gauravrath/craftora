<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $adminRole = Role::firstOrCreate([
            'slug' => 'admin',
        ], [
            'name' => 'Admin',
            'description' => 'Platform administrator',
            'is_system' => true,
        ]);

        $customerRole = Role::firstOrCreate([
            'slug' => 'customer',
        ], [
            'name' => 'Customer',
            'description' => 'Standard platform customer',
            'is_system' => true,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role_id' => $customerRole->id,
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role_id' => $adminRole->id,
        ]);

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'basic'],
            [
                'name' => 'Basic',
                'price' => 19.99,
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'description' => 'Starter protection and monitoring plan',
            ]
        );

        SubscriptionPlan::firstOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 49.99,
                'billing_cycle' => 'monthly',
                'status' => 'active',
                'description' => 'Advanced protection and emergency response',
            ]
        );
    }
}
