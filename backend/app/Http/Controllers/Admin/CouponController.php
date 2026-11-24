<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $coupons = $query
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->paginate(10)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $code = strtoupper(Str::random(9));
        $ifExists = Coupon::where('code', $code)->exists();

        while ($ifExists) {
            $code = strtoupper(Str::random(9));
            $ifExists = Coupon::where('code', $code)->exists();
        }
        return view('admin.coupons.create', compact('code'));
    }

    /**
     * Store new coupon.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCoupon($request);

        $validated['id'] = (string) Str::uuid();
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['used_count'] = 0;

        Coupon::create($validated);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Show coupon details.
     */
    public function show(string $id)
    {
        $coupon = Coupon::findOrFail($id);

        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show edit form.
     */
    public function edit(string $id)
    {
        $coupon = Coupon::findOrFail($id);

        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update coupon.
     */
    public function update(Request $request, string $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $this->validateCoupon($request, $coupon->id);

        $validated['code'] = strtoupper(trim($validated['code']));

        // Do not overwrite used_count from the edit form.
        $coupon->update($validated);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Soft delete coupon.
     */
    public function destroy(string $id)
    {
        $coupon = Coupon::findOrFail($id);

        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Shared validation rules.
     */
    private function validateCoupon(Request $request, ?string $couponId = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('coupons', 'code')->ignore($couponId, 'id'),
            ],

            'type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],

            'value' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999.99',
            ],

            'min_order_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],

            'max_discount_amount' => [
                'nullable',
                'numeric',
                'gt:0',
                'max:9999999999.99',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:starts_at',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        if (
            $validated['type'] === 'percentage' &&
            (float) $validated['value'] > 100
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'value' => 'Percentage coupon value cannot exceed 100.',
            ]);
        }

        if (
            isset($validated['min_order_amount'], $validated['max_discount_amount']) &&
            $validated['max_discount_amount'] > 0 &&
            $validated['min_order_amount'] < 0
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'min_order_amount' => 'Minimum order amount cannot be negative.',
            ]);
        }

        return $validated;
    }
}
