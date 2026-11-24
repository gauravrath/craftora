<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function categories()
    {
        return ApiResponse::success($this->activeCategories());
    }

    public function storefrontCategories()
    {
        $categories = $this->activeCategories()
            ->map(function (Category $category) {
                if ($category->image_url && ! Str::startsWith($category->image_url, ['http://', 'https://'])) {
                    $category->image_url = Storage::disk('s3')->temporaryUrl($category->image_url, now()->addMinutes(30));
                }

                return $category;
            });

        return ApiResponse::success($categories);
    }

    private function activeCategories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'image_url']);
    }
}
