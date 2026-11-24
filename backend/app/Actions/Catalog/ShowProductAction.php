<?php

namespace App\Actions\Catalog;

use App\Actions\BaseAction;
use App\Models\Product;

class ShowProductAction extends BaseAction
{
    public function execute(...$args): mixed
    {
        $slug = $args[0];
        return Product::query()
            ->with(['category', 'images', 'variants', 'inventory'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }
}
