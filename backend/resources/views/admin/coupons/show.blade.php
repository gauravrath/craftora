@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
    <main class="app-main">
        <div class="app-content">
          <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Coupon Details</h3>

                <div class="d-flex gap-2">
                    <a href="{{ route('coupons.edit', $coupon->id) }}"
                    class="btn btn-warning">
                        Edit Coupon
                    </a>

                    <a href="{{ route('coupons.index') }}"
                    class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong>{{ $coupon->code }}</strong>

                    @if($coupon->status === 'active')
                        <span class="badge bg-success ms-2">Active</span>
                    @else
                        <span class="badge bg-danger ms-2">Inactive</span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        <div class="col-md-4">
                            <strong>Coupon ID</strong>
                            <p>{{ $coupon->id }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Coupon Code</strong>
                            <p>{{ $coupon->code }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Discount Type</strong>
                            <p>{{ ucfirst($coupon->type) }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Discount Value</strong>
                            <p>
                                @if($coupon->type === 'percentage')
                                    {{ number_format((float) $coupon->value, 2) }}%
                                @else
                                    {{ number_format((float) $coupon->value, 2) }}
                                @endif
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Minimum Order Amount</strong>
                            <p>
                                {{ $coupon->min_order_amount !== null
                                    ? number_format((float) $coupon->min_order_amount, 2)
                                    : 'No minimum' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Maximum Discount Amount</strong>
                            <p>
                                {{ $coupon->max_discount_amount !== null
                                    ? number_format((float) $coupon->max_discount_amount, 2)
                                    : 'No maximum cap' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Usage Limit</strong>
                            <p>{{ $coupon->usage_limit ?? 'Unlimited' }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Times Used</strong>
                            <p>{{ $coupon->used_count }}</p>
                        </div>

                        <div class="col-md-4">
                            <strong>Remaining Uses</strong>
                            <p>
                                {{ $coupon->usage_limit !== null
                                    ? max(0, $coupon->usage_limit - $coupon->used_count)
                                    : 'Unlimited' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Start Date</strong>
                            <p>
                                {{ $coupon->starts_at?->format('d M Y, h:i A') ?? 'No start date' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Expiry Date</strong>
                            <p>
                                {{ $coupon->expires_at?->format('d M Y, h:i A') ?? 'No expiry date' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Created At</strong>
                            <p>
                                {{ $coupon->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="col-md-4">
                            <strong>Updated At</strong>
                            <p>
                                {{ $coupon->updated_at?->format('d M Y, h:i A') ?? 'N/A' }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    
<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
