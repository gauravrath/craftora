<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'short_description')) {
                $table->text('short_description')->nullable()->after('description');
            }

            if (! Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 12, 2)->nullable()->after('price');
            }

            if (! Schema::hasColumn('products', 'stock_quantity')) {
                $table->unsignedInteger('stock_quantity')->default(0)->after('discount_price');
            }

            if (! Schema::hasColumn('products', 'video_urls')) {
                $table->json('video_urls')->nullable()->after('stock_quantity');
            }

            if (! Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('video_urls');
            }

            if (! Schema::hasColumn('products', 'features')) {
                $table->json('features')->nullable()->after('specifications');
            }

            if (! Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 3)->nullable()->after('features');
            }

            if (! Schema::hasColumn('products', 'dimensions')) {
                $table->string('dimensions')->nullable()->after('weight');
            }

            if (! Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('dimensions');
            }

            if (! Schema::hasColumn('products', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }

            if (! Schema::hasColumn('products', 'seo_keywords')) {
                $table->text('seo_keywords')->nullable()->after('seo_description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [
                'short_description',
                'discount_price',
                'stock_quantity',
                'video_urls',
                'specifications',
                'features',
                'weight',
                'dimensions',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
