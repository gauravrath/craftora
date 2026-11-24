<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Catalog\CreateProductAction;
use App\Actions\Catalog\DeleteProductAction;
use App\Actions\Catalog\ListProductsAction;
use App\Actions\Catalog\ShowProductAction;
use App\Actions\Catalog\UpdateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\IndexProductsRequest;
use App\Http\Requests\Catalog\StoreProductRequest;
use App\Http\Requests\Catalog\UpdateProductRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ListProductsAction $listProductsAction,
        protected ShowProductAction $showProductAction,
        protected CreateProductAction $createProductAction,
        protected UpdateProductAction $updateProductAction,
        protected DeleteProductAction $deleteProductAction,
    ) {}

    public function index(IndexProductsRequest $request): JsonResponse
    {
        $products = $this->listProductsAction->execute($request);

        return ApiResponse::paginated($products, 'Products retrieved successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $product = $this->showProductAction->execute($slug);

        return ApiResponse::success(
            ProductResource::make($product->load(['category', 'images', 'variants'])),
            'Product retrieved successfully.'
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        if (($request->user()?->role?->slug ?? null) !== 'admin') {
            return ApiResponse::error('This action is unauthorized.', 403, []);
        }

        $product = $this->createProductAction->execute($request->validated());

        return ApiResponse::success(
            ProductResource::make($product->load(['category', 'images', 'variants'])),
            'Product created successfully.',
            201
        );
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        if (($request->user()?->role?->slug ?? null) !== 'admin') {
            return ApiResponse::error('This action is unauthorized.', 403, []);
        }

        $updatedProduct = $this->updateProductAction->execute($product, $request->validated());

        return ApiResponse::success(
            ProductResource::make($updatedProduct->load(['category', 'images', 'variants'])),
            'Product updated successfully.'
        );
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        if (($request->user()?->role?->slug ?? null) !== 'admin') {
            return ApiResponse::error('This action is unauthorized.', 403, []);
        }

        if (! $this->deleteProductAction->execute($product)) {
            return ApiResponse::error('Product could not be deleted.', 400);
        }

        return ApiResponse::success(null, 'Product deleted successfully.');
    }
}
