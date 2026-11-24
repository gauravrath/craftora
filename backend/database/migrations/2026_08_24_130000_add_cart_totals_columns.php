<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('status');
            $table->string('coupon_type')->nullable()->after('coupon_code');
            $table->decimal('coupon_value', 12, 2)->nullable()->after('coupon_type');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('coupon_value');
            $table->decimal('shipping_amount', 12, 2)->default(0)->after('discount_amount');
            $table->decimal('tax_rate', 5, 2)->default(8.00)->after('shipping_amount');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'coupon_type', 'coupon_value', 'discount_amount', 'shipping_amount', 'tax_rate']);
        });
    }
};
