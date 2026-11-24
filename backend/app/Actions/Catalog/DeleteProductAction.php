<?php

namespace App\Actions\Catalog;

use App\Actions\BaseAction;
use App\Models\Product;

class DeleteProductAction extends BaseAction
{
    public function execute(...$args): mixed
    {
        $product = $args[0];
        return (bool) $product->delete();
    }
}
