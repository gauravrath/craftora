@php
    $isEdit = isset($inventory);

    $selectedProduct = old(
        'product_id',
        $inventory->product_id ?? ''
    );

    $selectedVariant = old(
        'product_variant_id',
        $inventory->product_variant_id ?? ''
    );
@endphp

<div class="row">

    <div class="mb-3 col-md-6">
        <label class="form-label">Product <span class="text-danger">*</span></label>

        <select name="product_id" id="product_id"
                class="form-select" required>
            <option value="">Select Product</option>

            @foreach($products as $product)
                <option value="{{ $product->id }}"
                    @selected($selectedProduct == $product->id)>
                    {{ $product->name }}
                    @if($product->sku)
                        ({{ $product->sku }})
                    @endif
                </option>
            @endforeach
        </select>

        @error('product_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Product Variant</label>

        <select name="product_variant_id" id="product_variant_id"
                class="form-select">
            <option value="">No Variant</option>

            @foreach($variants as $variant)
                <option value="{{ $variant->id }}"
                        data-product="{{ $variant->product_id }}"
                        @selected($selectedVariant == $variant->id)>
                    {{ $variant->name }}
                    @if($variant->sku)
                        ({{ $variant->sku }})
                    @endif
                </option>
            @endforeach
        </select>

        @error('product_variant_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Quantity on Hand *</label>

        <input type="number" name="quantity_on_hand"
               class="form-control" min="0" required
               value="{{ old('quantity_on_hand', $inventory->quantity_on_hand ?? 0) }}">

        @error('quantity_on_hand')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Reserved Quantity *</label>

        <input type="number" name="reserved_quantity"
               class="form-control" min="0" required
               value="{{ old('reserved_quantity', $inventory->reserved_quantity ?? 0) }}">

        @error('reserved_quantity')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-4">
        <label class="form-label">Reorder Level *</label>

        <input type="number" name="reorder_level"
               class="form-control" min="0" required
               value="{{ old('reorder_level', $inventory->reorder_level ?? 0) }}">

        @error('reorder_level')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Warehouse Code</label>

        <input type="text" name="warehouse_id"
               class="form-control"
               value="{{ old('warehouse_id', $inventory->warehouse_id ?? '') }}">

        @error('warehouse_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Location / Shelf</label>

        <input type="text" name="location"
               class="form-control"
               placeholder="Example: A-01-Shelf-2"
               value="{{ old('location', $inventory->location ?? '') }}">

        @error('location')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 col-md-6">
        <label class="form-label">Last Restocked At</label>

        <input type="datetime-local" name="last_restocked_at"
               class="form-control"
               value="{{ old(
                    'last_restocked_at',
                    isset($inventory) && $inventory->last_restocked_at
                        ? $inventory->last_restocked_at->format('Y-m-d\TH:i')
                        : ''
               ) }}">

        @error('last_restocked_at')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

</div>

<div class="mt-3">
    <button type="submit" class="btn btn-success">
        {{ $isEdit ? 'Update Inventory' : 'Save Inventory' }}
    </button>

    <a href="{{ route('inventory.index') }}"
       class="btn btn-secondary">
        Cancel
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('product_id');
    const variantSelect = document.getElementById('product_variant_id');

    function filterVariants() {
        const productId = productSelect.value;

        Array.from(variantSelect.options).forEach(function (option) {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.product !== productId;
        });

        const selectedOption =
            variantSelect.options[variantSelect.selectedIndex];

        if (selectedOption && selectedOption.hidden) {
            variantSelect.value = '';
        }
    }

    productSelect.addEventListener('change', filterVariants);
    filterVariants();
});
</script>