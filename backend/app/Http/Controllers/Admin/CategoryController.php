<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Category;
use Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderByDesc('id')->paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', 1)
            ->get();

        return view('admin.category.create', compact('parentCategories'));
    }

    // Store new category
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:categories,slug',
            ],
            'description' => ['nullable', 'string'],
            'image_url' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:102400'],
            'is_active' => ['required', 'boolean'],
        ]);
        
        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Ensure generated slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;

        while (Category::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter++;
        }

        $file = $request->file('image_url');

        $path = Storage::disk('s3')->putFile(
            'uploads/category',
            $file
        );

        $validated['image_url'] = $path;
        
        Category::create($validated);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category created successfully.');
    }

    // Show one category
    public function show(Category $category)
    {
        $category = Category::findOrFail($category->id);
        $image = $category->imagePreviewUrl();
        return view('admin.category.view', compact('category', 'image'));
    }

    // Show edit form
    public function edit(Category $category)
    {
        $image = $category->imagePreviewUrl();
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->where('is_active', 1)
            ->get();

        return view('admin.category.edit', compact(
            'category',
            'parentCategories',
            'image'
        ));
    }

    // Update category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->ignore($category->id),
            ],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:102400'],
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['slug'] = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        $originalSlug = $validated['slug'];
        $counter = 1;

        while (
            Category::where('slug', $validated['slug'])
                ->where('id', '!=', $category->id)
                ->exists()
        ) {
            $validated['slug'] = $originalSlug . '-' . $counter++;
        }
        unset($validated['image_url']);

        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $path = Storage::disk('s3')->putFile(
                'uploads/category',
                $file
            );
            if ($path === false) {
                throw new \RuntimeException('Category image could not be uploaded.');
            }
            $validated['image_url'] = $path;
        }
        $category->update($validated);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category updated successfully.');
    }

    // Soft delete category
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('category.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function changeStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
        ]);
    }
}
