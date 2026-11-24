<?php

namespace App\Actions\Catalog;

use App\Actions\BaseAction;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class CreateProductAction extends BaseAction
{
    public function execute(...$args): mixed
    {
        $payload = $args[0] ?? [];
        $product = Product::create([
            'category_id' => $payload['category_id'] ?? null,
            'name' => $payload['name'],
            'slug' => $payload['slug'] ?? Str::slug($payload['name']),
            'brand' => $payload['brand'] ?? null,
            'sku' => $payload['sku'] ?? null,
            'price' => $payload['price'],
            'compare_price' => $payload['discount_price'] ?? $payload['compare_price'] ?? null,
            'description' => $payload['description'] ?? null,
            'status' => $payload['status'] ?? 'draft',
            'type' => $payload['type'] ?? 'physical',
            'is_featured' => $payload['is_featured'] ?? false,
            'is_active' => $payload['is_active'] ?? true,
            'sort_order' => $payload['sort_order'] ?? 0,
        ]);

        if (array_key_exists('stock_quantity', $payload)) {
            $product->inventory()->updateOrCreate([], [
                'quantity_on_hand' => (int) $payload['stock_quantity'],
                'reserved_quantity' => 0,
                'reorder_level' => 0,
            ]);
        }

        if (! empty($payload['images'])) {
            foreach ($payload['images'] as $index => $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $imageData['image_path'] ?? null,
                    'image_url' => $imageData['image_url'] ?? null,
                    'alt_text' => $imageData['alt_text'] ?? null,
                    'is_primary' => (bool) ($imageData['is_primary'] ?? ($index === 0)),
                    'sort_order' => $imageData['sort_order'] ?? $index,
                ]);
            }
        }

        if (! empty($payload['variants'])) {
            foreach ($payload['variants'] as $variantData) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantData['name'],
                    'sku' => $variantData['sku'] ?? null,
                    'option_values' => $variantData['option_values'] ?? null,
                    'price' => $variantData['price'] ?? $product->price,
                    'compare_price' => $variantData['compare_price'] ?? null,
                    'stock' => $variantData['stock'] ?? 0,
                    'is_active' => $variantData['is_active'] ?? true,
                ]);
            }
        }

        $product->refresh();

        return $product;
    }
}
