<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    
    public function index()
    {
        // DB::enableQueryLog();
        $inventories = Inventory::query()
            ->join('products', 'inventory.product_id', '=', 'products.id')
            ->leftJoin(
                'product_variants',
                'inventory.product_variant_id',
                '=',
                'product_variants.id'
            )
            ->select([
                'inventory.*',
                'products.name as product_name',
                'product_variants.name as variant_name',
            ])
            ->orderByDesc('inventory.id')
            ->paginate(10);

        return view('admin.inventory.index', compact('inventories'));
    }

    // CREATE: Show form
    public function create()
    {
        $products = DB::table('products')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'sku']);

        $variants = DB::table('product_variants')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'product_id', 'name', 'sku']);

        return view('admin.inventory.create', compact(
            'products',
            'variants'
        ));
    }

    
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $this->validateInventory($request);

        $this->validateVariantProduct(
            $validated['product_id'],
            $validated['product_variant_id'] ?? null
        );

        Inventory::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Inventory created successfully.');
    }

    // EDIT: Show edit form
    public function edit(Inventory $inventory)
    {
        $products = DB::table('products')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name', 'sku']);

        $variants = DB::table('product_variants')
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'product_id', 'name', 'sku']);

        return view('admin.inventory.edit', compact(
            'inventory',
            'products',
            'variants'
        ));
    }

    // UPDATE: Update inventory
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $this->validateInventory($request);

        $this->validateVariantProduct(
            $validated['product_id'],
            $validated['product_variant_id'] ?? null
        );

        $inventory->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Inventory updated successfully.');
    }

    // DELETE: Soft delete inventory
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()
            ->back()
            ->with('success', 'Inventory deleted successfully.');
    }

    // Shared validation
    private function validateInventory(Request $request): array
    {
        return $request->validate([
            'product_id' => [
                'required',
                'string',
                'exists:products,id',
            ],
            'product_variant_id' => [
                'nullable',
                'string',
                'exists:product_variants,id',
            ],
            'quantity_on_hand' => [
                'required',
                'integer',
                'min:0',
            ],
            'reserved_quantity' => [
                'required',
                'integer',
                'min:0',
                'lte:quantity_on_hand',
            ],
            'reorder_level' => [
                'required',
                'integer',
                'min:0',
            ],
            'warehouse_id' => [
                'nullable',
                'string',
                'max:255',
            ],
            'location' => [
                'nullable',
                'string',
                'max:255',
            ],
            'last_restocked_at' => [
                'nullable',
                'date',
            ],
        ]);
    }

    // Ensure the selected variant belongs to the selected product
    private function validateVariantProduct(
        string $productId,
        ?string $variantId
    ): void {
        if (!$variantId) {
            return;
        }

        $belongsToProduct = DB::table('product_variants')
            ->where('id', $variantId)
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->exists();

        if (!$belongsToProduct) {
            throw ValidationException::withMessages([
                'product_variant_id' =>
                    'Selected variant does not belong to this product.',
            ]);
        }
    }
}