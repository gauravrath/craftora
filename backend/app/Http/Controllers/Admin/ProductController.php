<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'variants', 'images'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->is_active);
            })
            ->orderBy('sort_order')
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();
        $categories = Category::where('is_active', 1)->get();
        return view('admin.product.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', 1)->get();

        return view('admin.product.create', compact('categories'));
    }


    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'images']);
        $categories = Category::where('is_active', 1)->get();
        return view('admin.product.view', compact('product', 'categories'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', 1)->get();

        $product->load(['variants', 'images']);

        return view('admin.product.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore($product->id, 'id'),
            ],
            'brand' => ['nullable', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($product->id, 'id'),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'video_urls' => ['nullable', 'json'],
            'specifications' => ['nullable', 'json'],
            'features' => ['nullable', 'json'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'seo_keywords' => ['nullable', 'string'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'type' => ['required', 'string', 'max:255'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Ensure slug is unique after formatting
        $originalSlug = $validated['slug'];
        $counter = 1;

        while (
            Product::where('slug', $validated['slug'])
                ->where('id', '!=', $product->id)
                ->exists()
        ) {
            $validated['slug'] = $originalSlug . '-' . $counter++;
        }

        $product->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Product updated successfully.');
    }

    // Soft delete product
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->back()
            ->with('success', 'Product deleted successfully.');
    }

    public function bulkUpload()
    {
        $products = Product::with('category')->latest('created_at')->get();

        return view('admin.product.bulk_upload', compact('products'));
    }

    public function sampleDownload()
    {
        $filePath = storage_path('app/public/sample_products.csv');
        return response()->download($filePath);
    }

    public function store(Request $request)
    {
        // 1. Validate product form
        $validated = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:255',
            'slug'              => 'required|string|max:255|unique:products,slug',
            'brand'             => 'nullable|string|max:255',
            'sku'               => 'nullable|string|max:255',
            'price'             => 'required|numeric|min:0',
            'discount_price'    => 'nullable|numeric|min:0|lte:price',
            'compare_price'     => 'nullable|numeric|min:0',
            'stock_quantity'    => 'required|integer|min:0',

            'description'       => 'nullable|string',
            'short_description' => 'nullable|string',
            'seo_title'         => 'nullable|string|max:255',
            'seo_description'   => 'nullable|string',
            'seo_keywords'      => 'nullable|string',

            'weight'            => 'nullable|string|max:255',
            'dimensions'        => 'nullable|string|max:255',

            'video_urls'        => 'nullable',
            'specifications'    => 'nullable',
            'features'          => 'nullable',

            'status'            => [
                'required',
                Rule::in(['draft', 'published', 'active', 'inactive']),
            ],
            'type'              => [
                'required',
                Rule::in(['physical', 'digital']),
            ],
            'is_featured'       => 'nullable|boolean',
            'is_active'         => 'nullable|boolean',
            'sort_order'        => 'nullable|integer|min:0',

            // Product images: <input type="file" name="images[]">
            'images'            => 'nullable|array',
            'images.*'          => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            'primary_image'     => 'nullable|integer|min:0',

            // Product variants: variants[0][name], etc.
            'variants'                  => 'nullable|array',
            'variants.*.name'            => 'required_with:variants|string|max:255',
            'variants.*.sku'             => 'nullable|string|max:255',
            'variants.*.price'           => 'nullable|numeric|min:0',
            'variants.*.compare_price'   => 'nullable|numeric|min:0',
            'variants.*.stock'           => 'nullable|integer|min:0',
            'variants.*.option_values'   => 'nullable',
            'variants.*.is_active'       => 'nullable|boolean',
        ]);

        $uploadedPaths = [];

        try {
            $productId = (string) Str::uuid();

            DB::transaction(function () use (
                $request,
                $validated,
                $productId,
                &$uploadedPaths
            ) {
                // 2. Prepare product data
                $productData = [
                    'id'                => $productId,
                    'category_id'       => $validated['category_id'],
                    'name'              => $validated['name'],
                    'slug'              => $validated['slug'],
                    'brand'             => $validated['brand'] ?? null,
                    'sku'               => $validated['sku'] ?? null,
                    'price'             => $validated['price'],
                    'discount_price'    => $validated['discount_price'] ?? null,
                    'compare_price'     => $validated['compare_price'] ?? null,
                    'stock_quantity'    => $validated['stock_quantity'],

                    'description'       => $validated['description'] ?? null,
                    'short_description' => $validated['short_description'] ?? null,
                    'seo_title'         => $validated['seo_title'] ?? null,
                    'seo_description'   => $validated['seo_description'] ?? null,
                    'seo_keywords'      => $validated['seo_keywords'] ?? null,
                    'weight'            => $validated['weight'] ?? null,
                    'dimensions'        => $validated['dimensions'] ?? null,

                    'video_urls'        => $this->jsonValue($request->input('video_urls')),
                    'specifications'    => $this->jsonValue($request->input('specifications')),
                    'features'          => $this->jsonValue($request->input('features')),

                    'status'            => $validated['status'],
                    'type'              => $validated['type'],
                    'is_featured'       => $request->boolean('is_featured'),
                    'is_active'         => $request->boolean('is_active'),
                    'sort_order'        => $validated['sort_order'] ?? 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                // 3. Save product
                DB::table('products')->insert($productData);

                // 4. Save product images
                $images = $request->file('images', []);
                $primaryIndex = (int) $request->input('primary_image', 0);

                foreach ($images as $index => $image) {
                    if (!$image || !$image->isValid()) {
                        continue;
                    }

                    $path = $image->store('products', 'public');

                    if (!$path) {
                        throw new \RuntimeException(
                            'Product image could not be uploaded.'
                        );
                    }

                    $uploadedPaths[] = $path;

                    DB::table('product_images')->insert([
                        'product_id' => $productId,
                        'image_path' => $path,
                        'image_url'  => null,
                        'alt_text'   => $validated['name'],
                        'is_primary' => ((int) $index === $primaryIndex) ? 1 : 0,
                        'sort_order' => $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // 5. Save product variants
                foreach ($request->input('variants', []) as $variant) {
                    $optionValues = $this->jsonValue(
                        $variant['option_values'] ?? null
                    );

                    DB::table('product_variants')->insert([
                        'id'            => (string) Str::uuid(),
                        'product_id'    => $productId,
                        'name'          => $variant['name'],
                        'sku'           => $variant['sku'] ?? null,
                        'option_values' => $optionValues,
                        'price'         => $variant['price'] ?? null,
                        'compare_price' => $variant['compare_price'] ?? null,
                        'stock'         => $variant['stock'] ?? 0,
                        'is_active'     => isset($variant['is_active'])
                            ? (int) $variant['is_active']
                            : 1,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            });

            return redirect()
                ->back()
                ->with('success', 'Product saved successfully.');

        } catch (Throwable $e) {
            // Remove uploaded files if database saving fails
            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unable to save product. Please check the form and try again.');
        }
    }

    /**
     * Convert arrays to JSON for database storage.
     * Existing JSON strings are preserved when valid.
     */
    private function jsonValue($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }

            // Store ordinary text as a JSON string
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    
    public function save(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array', 'min:1', 'max:500'],
        ]);

        $allowed = [
            'category_id', 'name', 'slug', 'brand', 'sku',
            'price', 'discount_price', 'stock_quantity',
            'video_urls', 'specifications', 'features',
            'weight', 'dimensions', 'seo_title',
            'seo_description', 'seo_keywords', 'compare_price',
            'description', 'short_description', 'status', 'type',
            'is_featured', 'is_active', 'sort_order',
        ];

        $jsonFields = [
            'video_urls', 'specifications', 'features',
        ];

        $products = [];
        $errors = [];
        $seenProducts = ['slug' => [], 'sku' => []];
        $variantSkus = [];

        foreach ($request->products as $row) {
            $rowNumber = count($products) + count($errors) + 1;
            if (!is_array($row)) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Invalid product row.'],
                ];
                continue;
            }

            $data = array_intersect_key($row, array_flip($allowed));
            foreach ($data as $key => $value) {
                if ($value === '') {
                    $data[$key] = null;
                }
            }
            $data['status'] ??= 'draft';
            $data['type'] ??= 'physical';
            $data['is_featured'] ??= 0;
            $data['is_active'] ??= 1;
            $data['sort_order'] ??= 0;

            $rules = [
                'id' => ['prohibited'],
                'category_id' => [
                    'nullable', 'integer',
                    Rule::exists('categories', 'id'),
                ],
                'name' => ['required', 'string', 'max:255'],
                'slug' => [
                    'required', 'string', 'max:255',
                    Rule::unique('products', 'slug'),
                ],
                'brand' => ['nullable', 'string', 'max:255'],
                'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')],
                'price' => ['required', 'numeric', 'min:0'],
                'discount_price' => ['nullable', 'numeric', 'min:0'],
                'compare_price' => ['nullable', 'numeric', 'min:0'],
                'stock_quantity' => ['required', 'integer', 'min:0'],
                'weight' => ['nullable', 'numeric', 'min:0'],
                'dimensions' => ['nullable', 'string', 'max:255'],
                'seo_title' => ['nullable', 'string', 'max:255'],
                'seo_description' => ['nullable', 'string'],
                'seo_keywords' => ['nullable', 'string'],
                'description' => ['nullable', 'string'],
                'short_description' => ['nullable', 'string'],
                'status' => ['required', Rule::in([
                    'draft', 'published', 'active', 'inactive',
                ])],
                'type' => ['required', Rule::in([
                    'physical', 'digital',
                ])],
                'is_featured' => ['nullable', 'boolean'],
                'is_active' => ['nullable', 'boolean'],
                'sort_order' => ['nullable', 'integer', 'min:0'],
                'image_paths' => ['nullable', 'string'],
                'variants_json' => ['nullable', 'string'],
            ];

            $input = $data + array_intersect_key($row, array_flip(['id', 'image_paths', 'variants_json']));
            $validator = Validator::make($input, $rules);
            $validator->fails();
            $messages = $validator->errors();

            foreach ($jsonFields as $field) {
                if (isset($data[$field])) {
                    if (is_string($data[$field])) {
                        json_decode($data[$field]);
                    }
                    if (!is_string($data[$field]) || json_last_error() !== JSON_ERROR_NONE) {
                        $messages->add($field, "$field must contain valid JSON.");
                    }
                }
            }

            foreach (['slug', 'sku'] as $field) {
                if (isset($data[$field]) && is_string($data[$field]) && $data[$field] !== '') {
                    $value = mb_strtolower($data[$field]);
                    if (isset($seenProducts[$field][$value])) {
                        $messages->add($field, "Duplicate product $field in the import.");
                    }
                    $seenProducts[$field][$value] = true;
                }
            }

            $paths = [];
            if (isset($row['image_paths']) && is_string($row['image_paths'])) {
                $paths = array_values(array_filter(array_map('trim', explode(';', $row['image_paths'])), fn ($path) => $path !== ''));
                foreach ($paths as $path) {
                    if (mb_strlen($path) > 255) {
                        $messages->add('image_paths', 'Each image path must not exceed 255 characters.');
                    }
                }
            }

            $variants = [];
            if (isset($row['variants_json']) && $row['variants_json'] !== '' && is_string($row['variants_json'])) {
                $decoded = json_decode($row['variants_json']);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                    $messages->add('variants_json', 'Variants must be a JSON array of objects.');
                } else {
                    foreach ($decoded as $variantIndex => $variant) {
                        if (!is_object($variant)) {
                            $messages->add('variants_json', 'Each variant must be a JSON object.');
                            continue;
                        }
                        $variant = (array) $variant;
                        foreach ($variant as $field => $value) {
                            if ($value === '') {
                                $variant[$field] = null;
                            }
                        }
                        $variantValidator = Validator::make($variant, [
                            'name' => ['required', 'string', 'max:255'],
                            'sku' => ['nullable', 'string', 'max:255', Rule::unique('product_variants', 'sku')],
                            'option_values' => ['nullable', function ($attribute, $value, $fail) {
                                if (!is_array($value) && !is_object($value)) {
                                    $fail('The option values must be a JSON object or array.');
                                }
                            }],
                            'price' => ['nullable', 'numeric', 'min:0'],
                            'compare_price' => ['nullable', 'numeric', 'min:0'],
                            'stock' => ['nullable', 'integer', 'min:0'],
                            'is_active' => ['nullable', 'boolean'],
                        ]);
                        foreach ($variantValidator->errors()->all() as $message) {
                            $messages->add('variants_json', 'Variant '.($variantIndex + 1).': '.$message);
                        }
                        if (isset($variant['sku']) && is_string($variant['sku']) && $variant['sku'] !== '') {
                            $sku = mb_strtolower($variant['sku']);
                            if (isset($variantSkus[$sku])) {
                                $messages->add('variants_json', 'Duplicate variant SKU in the import.');
                            }
                            $variantSkus[$sku] = true;
                        }
                        $variants[] = $variant;
                    }
                }
            }

            if ($messages->isNotEmpty()) {
                $errors[] = [
                    'row' => $rowNumber,
                    'errors' => $messages->all(),
                ];
                continue;
            }

            $products[] = ['data' => $data, 'paths' => $paths, 'variants' => $variants];
        }

        if ($errors) {
            return response()->json([
                'success' => false,
                'message' => 'Please correct the invalid rows.',
                'errors' => $errors,
            ], 422);
        }

        try {
            DB::transaction(function () use ($products, $jsonFields) {
                foreach ($products as $product) {
                    $id = (string) Str::uuid();
                    $data = $product['data'];
                    foreach ($jsonFields as $field) {
                        if (isset($data[$field])) {
                            $data[$field] = json_encode(json_decode($data[$field]), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                        }
                    }

                    $data['id'] = $id;
                    $data['created_at'] = now();
                    $data['updated_at'] = now();
                    DB::table('products')->insert($data);

                    $images = [];
                    foreach ($product['paths'] as $i => $path) {
                        $images[] = [
                            'product_id' => $id,
                            'image_path' => $path,
                            'image_url' => null,
                            'alt_text' => $data['name'],
                            'is_primary' => $i === 0,
                            'sort_order' => $i,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if ($images) {
                        foreach (array_chunk($images, 100) as $chunk) {
                            DB::table('product_images')->insert($chunk);
                        }
                    }

                    $variants = [];
                    foreach ($product['variants'] as $variant) {
                        $variants[] = [
                            'id' => (string) Str::uuid(),
                            'product_id' => $id,
                            'name' => $variant['name'],
                            'sku' => ($variant['sku'] ?? '') !== '' ? $variant['sku'] : null,
                            'option_values' => isset($variant['option_values'])
                                ? json_encode($variant['option_values'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
                                : null,
                            'price' => $variant['price'] ?? 0,
                            'compare_price' => $variant['compare_price'] ?? null,
                            'stock' => $variant['stock'] ?? 0,
                            'is_active' => $variant['is_active'] ?? 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    foreach (array_chunk($variants, 80) as $chunk) {
                        DB::table('product_variants')->insert($chunk);
                    }
                }
            });

            $saved = count($products);
            return response()->json([
                'success' => true,
                'message' => "$saved product rows saved successfully.",
                'saved' => $saved,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Save failed. No changes were committed.',
            ], 500);
        }
    }

    public function changeStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $product->is_active,
        ]);
    }
}
