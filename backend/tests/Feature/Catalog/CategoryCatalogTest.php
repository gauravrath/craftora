<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_categories_are_active_sorted_and_have_usable_media_urls(): void
    {
        Storage::fake('s3');
        Storage::disk('s3')->buildTemporaryUrlsUsing(
            fn (string $path) => 'https://storage.example.test/'.$path.'?signed=1'
        );

        Category::create(['name' => 'Zebra', 'slug' => 'zebra', 'is_active' => true]);
        Category::create([
            'name' => 'Public',
            'slug' => 'public',
            'is_active' => true,
            'image_url' => 'https://example.test/public.jpg',
        ]);
        $category = Category::create([
            'name' => 'Art',
            'slug' => 'art',
            'is_active' => true,
            'image_url' => 'uploads/category/art.jpg',
        ]);
        Category::create(['name' => 'Hidden', 'slug' => 'hidden', 'is_active' => false]);
        Category::create(['name' => 'Deleted', 'slug' => 'deleted', 'is_active' => true])->delete();

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.0.id', $category->id)
            ->assertJsonPath('data.0.image_url', 'https://storage.example.test/uploads/category/art.jpg?signed=1')
            ->assertJsonPath('data.1.image_url', 'https://example.test/public.jpg')
            ->assertJsonPath('data.2.name', 'Zebra')
            ->assertJsonPath('data.2.image_url', null);

        $this->getJson('/api/categories')
            ->assertOk()
            ->assertJsonPath('data.0.image_url', 'uploads/category/art.jpg');
    }
}
