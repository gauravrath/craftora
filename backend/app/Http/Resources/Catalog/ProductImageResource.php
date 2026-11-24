<?php

namespace App\Http\Resources\Catalog;

use App\Http\Resources\ApiResource;

class ProductImageResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'alt_text' => $this->alt_text,
            'is_primary' => $this->is_primary,
            'sort_order' => $this->sort_order,
        ];
    }
}
