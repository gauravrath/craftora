<?php

namespace App\Actions\Catalog;

use App\Actions\BaseAction;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class UpdateProductAction extends BaseAction
{
    public function execute(...$args): mixed
    {
        $product = $args[0];
        $payload = $args[1] ?? [];
        $product->fill([
            'category_id' => $payload['category_id'] ?? $product->category_id,
            'name' => $payload['name'] ?? $product->name,
            'slug' => $payload['slug'] ?? $product->slug ?? Str::slug($product->name),
            'brand' => $payload['brand'] ?? $product->brand,
            'sku' => $payload['sku'] ?? $product->sku,
            'price' => $payload['price'] ?? $product->price,
            'compare_price' => $payload['discount_price'] ?? $payload['compare_price'] ?? $product->compare_price,
            'description' => $payload['description'] ?? $product->description,
            'status' => $payload['status'] ?? $product->status,
            'type' => $payload['type'] ?? $product->type,
            'is_featured' => $payload['is_featured'] ?? $product->is_featured,
            'is_active' => $payload['is_active'] ?? $product->is_active,
            'sort_order' => $payload['sort_order'] ?? $product->sort_order,
        ]);

        $product->save();

        if (array_key_exists('stock_quantity', $payload)) {
            $product->inventory()->updateOrCreate([], [
                'quantity_on_hand' => (int) $payload['stock_quantity'],
                'reserved_quantity' => 0,
                'reorder_level' => 0,
            ]);
        }

        if (array_key_exists('images', $payload)) {
            $product->images()->delete();

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

        if (array_key_exists('variants', $payload)) {
            $product->variants()->delete();

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

        return $product->refresh();
    }
}
