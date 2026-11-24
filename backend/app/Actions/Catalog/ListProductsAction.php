<?php

namespace App\Actions\Catalog;

use App\Actions\BaseAction;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ListProductsAction extends BaseAction
{
    public function execute(...$args): mixed
    {
        $request = $args[0] ?? new Request();
        $query = Product::query()
            ->with(['category', 'images', 'variants', 'inventory'])
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function (Builder $builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), function (Builder $query) use ($request) {
                $query->where('category_id', $request->input('category'));
            })
            ->when($request->filled('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('featured'), function (Builder $query) use ($request) {
                $query->where('is_featured', filter_var($request->input('featured'), FILTER_VALIDATE_BOOLEAN));
            })
            ->when($request->filled('min_price'), function (Builder $query) use ($request) {
                $query->where('price', '>=', (float) $request->input('min_price'));
            })
            ->when($request->filled('max_price'), function (Builder $query) use ($request) {
                $query->where('price', '<=', (float) $request->input('max_price'));
            })
            ->where('is_active', true);

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDir = $request->input('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($request->input('per_page', 15));
    }
}
