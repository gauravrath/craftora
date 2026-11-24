<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BulkProductImportTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Mug',
            'slug' => 'mug',
            'price' => '10.00',
            'stock_quantity' => '5',
        ], $overrides);
    }

    public function test_it_creates_products_with_multiple_images_and_variants(): void
    {
        $response = $this->postJson('/products/import/save', ['products' => [
            $this->product([
                'image_paths' => ' ; products/one.jpg; products/two.jpg',
                'variants_json' => json_encode([
                    ['name' => 'Small', 'sku' => 'SMALL', 'option_values' => ['size' => 'S'], 'price' => 12, 'stock' => 2],
                    ['name' => 'Large', 'sku' => 'LARGE', 'is_active' => false],
                ]),
            ]),
            $this->product(['name' => 'Plate', 'slug' => 'plate']),
        ]]);

        $response->assertOk()->assertJsonPath('saved', 2);
        $id = DB::table('products')->where('slug', 'mug')->value('id');
        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseCount('product_images', 2);
        $this->assertDatabaseCount('product_variants', 2);
        $this->assertDatabaseHas('product_images', ['product_id' => $id, 'image_path' => 'products/one.jpg', 'is_primary' => 1, 'sort_order' => 0]);
        $this->assertDatabaseHas('product_images', ['product_id' => $id, 'image_path' => 'products/two.jpg', 'is_primary' => 0, 'sort_order' => 1]);
        $this->assertDatabaseHas('product_variants', ['product_id' => $id, 'sku' => 'SMALL', 'stock' => 2]);
        $this->assertDatabaseHas('product_variants', ['product_id' => $id, 'sku' => 'LARGE', 'price' => 0, 'is_active' => 0]);
        $this->assertSame(['size' => 'S'], json_decode(DB::table('product_variants')->where('sku', 'SMALL')->value('option_values'), true));
    }

    public function test_invalid_variants_reject_the_entire_batch(): void
    {
        foreach (['bad json', '{}', '[1]', '[{"sku":"X"}]', '[{"name":"X","stock":-1}]'] as $variants) {
            $this->postJson('/products/import/save', ['products' => [
                $this->product(),
                $this->product(['slug' => 'other', 'variants_json' => $variants]),
            ]])->assertUnprocessable()->assertJsonPath('errors.0.row', 2);
            $this->assertDatabaseCount('products', 0);
        }
    }

    public function test_existing_ids_and_duplicate_values_are_rejected(): void
    {
        $existing = Product::create($this->product());
        $this->postJson('/products/import/save', ['products' => [
            $this->product(['id' => $existing->id, 'slug' => 'different']),
        ]])->assertUnprocessable();
        $this->assertDatabaseHas('products', ['id' => $existing->id, 'slug' => 'mug']);

        $this->postJson('/products/import/save', ['products' => [
            $this->product(['slug' => 'new', 'variants_json' => '[{"name":"A","sku":"SAME"}]']),
            $this->product(['slug' => 'new', 'variants_json' => '[{"name":"B","sku":"SAME"}]']),
        ]])->assertUnprocessable()->assertJsonPath('errors.0.row', 2);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_invalid_json_and_invalid_image_paths_are_rejected(): void
    {
        $this->postJson('/products/import/save', ['products' => [
            $this->product(['features' => '{invalid', 'image_paths' => str_repeat('x', 256)]),
        ]])->assertUnprocessable()
            ->assertJsonPath('errors.0.row', 1);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_database_failure_rolls_back_products_images_and_variants(): void
    {
        DB::listen(function ($query) {
            if (str_starts_with(strtolower($query->sql), 'insert into "product_variants"')) {
                throw new \RuntimeException('Simulated variant save failure.');
            }
        });

        $this->postJson('/products/import/save', ['products' => [
            $this->product([
                'image_paths' => 'products/one.jpg',
                'variants_json' => '[{"name":"Small"}]',
            ]),
        ]])->assertStatus(500)->assertJsonPath('success', false);

        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_images', 0);
        $this->assertDatabaseCount('product_variants', 0);
    }

    public function test_large_variant_sets_are_saved_in_batches(): void
    {
        $variants = [];
        for ($i = 1; $i <= 120; $i++) {
            $variants[] = ['name' => 'Variant '.$i, 'sku' => 'SKU-'.$i];
        }

        $this->postJson('/products/import/save', ['products' => [
            $this->product(['variants_json' => json_encode($variants)]),
        ]])->assertOk()->assertJsonPath('saved', 1);

        $this->assertDatabaseCount('product_variants', 120);
    }

    public function test_downloaded_sample_can_be_imported(): void
    {
        $file = fopen(storage_path('app/public/sample_products.csv'), 'r');
        $headers = fgetcsv($file, 0, ',', '"', '');
        $values = fgetcsv($file, 0, ',', '"', '');
        fclose($file);

        $this->postJson('/products/import/save', ['products' => [
            array_combine($headers, $values),
        ]])->assertOk()->assertJsonPath('saved', 1);

        $this->assertDatabaseCount('product_images', 2);
        $this->assertDatabaseCount('product_variants', 2);
    }
}
