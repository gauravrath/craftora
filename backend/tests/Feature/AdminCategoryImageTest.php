<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCategoryImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_pages_render_without_an_image(): void
    {
        $category = Category::create(['name' => 'No image', 'slug' => 'no-image']);

        foreach (['/category', "/category/{$category->id}", "/category/{$category->id}/edit"] as $path) {
            $this->get($path)->assertOk()->assertSee('No image');
        }
    }

    public function test_category_pages_show_signed_paths_and_public_urls(): void
    {
        Storage::fake('s3');
        $signedPaths = [];
        Storage::disk('s3')->buildTemporaryUrlsUsing(function (string $path) use (&$signedPaths) {
            $signedPaths[] = $path;

            return 'https://storage.example.test/'.$path.'?signed=1';
        });

        $stored = Category::create([
            'name' => 'Stored image',
            'slug' => 'stored-image',
            'image_url' => 'uploads/category/image.jpg',
        ]);
        $public = Category::create([
            'name' => 'Public image',
            'slug' => 'public-image',
            'image_url' => 'https://example.test/image.jpg',
        ]);

        $this->get('/category')->assertOk()
            ->assertSee('https://storage.example.test/uploads/category/image.jpg?signed=1')
            ->assertSee('https://example.test/image.jpg');

        foreach ([$stored, $public] as $category) {
            $url = $category->is($stored)
                ? 'https://storage.example.test/uploads/category/image.jpg?signed=1'
                : 'https://example.test/image.jpg';
            $this->get("/category/{$category->id}")->assertOk()->assertSee($url);
            $this->get("/category/{$category->id}/edit")->assertOk()->assertSee($url);
        }

        $this->assertSame(array_fill(0, 3, 'uploads/category/image.jpg'), $signedPaths);
    }

    public function test_edit_form_uploads_and_saves_the_category_image(): void
    {
        Storage::fake('s3');
        $category = Category::create(['name' => 'Original', 'slug' => 'original']);
        $file = UploadedFile::fake()->image('category.jpg');

        $this->get(route('category.edit', $category))->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="image_url"', false);

        $this->post(route('category.update', $category), [
            '_method' => 'PUT',
            'name' => 'Updated',
            'slug' => 'updated',
            'is_active' => '1',
            'image_url' => $file,
        ])->assertRedirect(route('category.index'))->assertSessionHasNoErrors();

        $category->refresh();
        $this->assertSame('Updated', $category->name);
        $this->assertSame('uploads/category/'.$file->hashName(), $category->image_url);
        Storage::disk('s3')->assertExists($category->image_url);
    }

    public function test_update_without_an_image_keeps_the_existing_path(): void
    {
        $category = Category::create([
            'name' => 'Original',
            'slug' => 'original',
            'image_url' => 'uploads/category/existing.jpg',
        ]);

        $this->post(route('category.update', $category), [
            '_method' => 'PUT',
            'name' => 'Updated',
            'slug' => 'updated',
            'is_active' => '1',
            'image_url' => '',
        ])->assertRedirect(route('category.index'))->assertSessionHasNoErrors();

        $this->assertSame('uploads/category/existing.jpg', $category->fresh()->image_url);
    }

    public function test_invalid_upload_is_reported_without_changing_the_category(): void
    {
        Storage::fake('s3');
        $category = Category::create(['name' => 'Original', 'slug' => 'original']);

        $this->from(route('category.edit', $category))->post(route('category.update', $category), [
            '_method' => 'PUT',
            'name' => 'Updated',
            'slug' => 'updated',
            'is_active' => '1',
            'image_url' => UploadedFile::fake()->create('invalid.txt', 1, 'text/plain'),
        ])->assertRedirect(route('category.edit', $category))->assertSessionHasErrors('image_url');

        $this->get(route('category.edit', $category))->assertOk()
            ->assertSee('The image url field must be a file of type');
        $this->assertSame('Original', $category->fresh()->name);
        $this->assertNull($category->fresh()->image_url);
    }
}
