<?php

namespace App\Http\Resources\Catalog;

use App\Http\Resources\ApiResource;

class ProductResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'discount_price' => $this->compare_price,
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'video_urls' => $this->video_urls,
            'specifications' => $this->specifications,
            'features' => $this->features,
            'weight' => $this->weight,
            'dimensions' => $this->dimensions,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
