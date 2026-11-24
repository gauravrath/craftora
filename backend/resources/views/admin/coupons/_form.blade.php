@php
    $coupon = $coupon ?? null;
@endphp

<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
        <input type="text"
               name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $code ?? $coupon?->code) }}"
               placeholder="e.g. DIWALI20"
               required readonly>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
        <select name="type"
                id="coupon_type"
                class="form-select @error('type') is-invalid @enderror"
                required>
            <option value="--">Select Discount Type</option>
            <option value="percentage" @if(($coupon?->type ?? 'percentage') === 'percentage') selected @endif>
                Percentage (%)
            </option>
            <option value="fixed" @if(($coupon?->type ?? 'percentage') === 'fixed') selected @endif>
                Fixed Amount
            </option>
        </select>
        @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
        <input type="number"
               name="value"
               class="form-control @error('value') is-invalid @enderror"
               value="{{ old('value', $coupon?->value) }}"
               min="0.01"
               max="9999999999.99"
               id="discount_value"
               step="0.01"
               required>
               
        @error('value')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Minimum Order Amount</label>
        <input type="number"
               name="min_order_amount"
               class="form-control @error('min_order_amount') is-invalid @enderror"
               value="{{ old('min_order_amount', $coupon?->min_order_amount) }}"
               id ="min_order_amount"
               min="0"
               step="0.01">
        @error('min_order_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Maximum Discount Amount</label>
        <input type="number"
               name="max_discount_amount"
               class="form-control @error('max_discount_amount') is-invalid @enderror"
               value="{{ old('max_discount_amount', $coupon?->max_discount_amount) }}"
               min="0.01"
               id="max_discount_amount"
               step="0.01">
        @error('max_discount_amount')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Usage Limit</label>
        <input type="number"
               name="usage_limit"
               class="form-control @error('usage_limit') is-invalid @enderror"
               value="{{ old('usage_limit', $coupon?->usage_limit) }}"
               min="1"
               step="1">
        <small class="text-muted">Blank = unlimited uses.</small>
        @error('usage_limit')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Start Date & Time</label>
        <input type="datetime-local"
               name="starts_at"
               class="form-control @error('starts_at') is-invalid @enderror"
               value="{{ old('starts_at', $coupon?->starts_at?->format('Y-m-d\TH:i')) }}">
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Expiry Date & Time</label>
        <input type="datetime-local"
               name="expires_at"
               class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ old('expires_at', $coupon?->expires_at?->format('Y-m-d\TH:i')) }}">
        @error('expires_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status"
                class="form-select @error('status') is-invalid @enderror"
                required>
            <option value="active"
                @selected(old('status', $coupon?->status ?? 'active') === 'active')>
                Active
            </option>
            <option value="inactive"
                @selected(old('status', $coupon?->status) === 'inactive')>
                Inactive
            </option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        {{ $coupon ? 'Update Coupon' : 'Create Coupon' }}
    </button>

    <a href="{{ route('coupons.index') }}"
       class="btn btn-secondary">
        Cancel
    </a>
</div>