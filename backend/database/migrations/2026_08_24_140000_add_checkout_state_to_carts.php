<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('total');
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('shipping_address_id');
            $table->json('shipping_address_json')->nullable()->after('billing_address_id');
            $table->json('billing_address_json')->nullable()->after('shipping_address_json');
            $table->string('shipping_method')->nullable()->after('billing_address_json');
            $table->decimal('shipping_cost', 12, 2)->default(0)->after('shipping_method');
            $table->string('payment_method')->nullable()->after('shipping_cost');
            $table->string('checkout_step')->nullable()->after('payment_method');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->nullOnDelete();
            $table->foreign('billing_address_id')->references('id')->on('addresses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['shipping_address_id']);
            $table->dropForeign(['billing_address_id']);
            $table->dropColumn([
                'shipping_address_id',
                'billing_address_id',
                'shipping_address_json',
                'billing_address_json',
                'shipping_method',
                'shipping_cost',
                'payment_method',
                'checkout_step',
            ]);
        });
    }
};
