@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
    <main class="app-main">        
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-11">
                        <h1 class="mb-0 fs-5">Coupons</h1>
                    </div>
                    <div class="col-sm-1">
                        <a href="{{ route('coupons.create') }}"
                        class="btn btn-primary">
                            + Add Coupon
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close"
                            data-bs-dismiss="alert"></button>
                </div>
            @endif
        <div class="app-content">
            <div class="container-fluid">
                <div class="card mb-4">
                    <div class="card-body">

                        <form method="GET" action="{{ route('coupons.index') }}">
                            <div class="row g-3">

                                <div class="col-md-5">
                                    <input type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Search coupon code or type"
                                        value="{{ request('search') }}">
                                </div>

                                <div class="col-md-3">
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="percentage"
                                            @selected(request('type') === 'percentage')>
                                            Percentage
                                        </option>
                                        <option value="fixed"
                                            @selected(request('type') === 'fixed')>
                                            Fixed
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="active"
                                            @selected(request('status') === 'active')>
                                            Active
                                        </option>
                                        <option value="inactive"
                                            @selected(request('status') === 'inactive')>
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        Search
                                    </button>

                                    <a href="{{ route('coupons.index') }}"
                                    class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Coupon Code</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Min. Order</th>
                                        <th>Max. Discount</th>
                                        <th>Usage</th>
                                        <th>Start Date</th>
                                        <th>Expiry Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($coupons as $coupon)
                                        <tr>
                                            <td>{{ $coupons->firstItem() + $loop->index }}</td>

                                            <td>
                                                <strong>{{ $coupon->code }}</strong>
                                            </td>

                                            <td>{{ ucfirst($coupon->type) }}</td>

                                            <td>
                                                @if($coupon->type === 'percentage')
                                                    {{ number_format((float) $coupon->value, 2) }}%
                                                @else
                                                    {{ number_format((float) $coupon->value, 2) }}
                                                @endif
                                            </td>

                                            <td>
                                                {{ $coupon->min_order_amount !== null
                                                    ? number_format((float) $coupon->min_order_amount, 2)
                                                    : 'No minimum' }}
                                            </td>

                                            <td>
                                                {{ $coupon->max_discount_amount !== null
                                                    ? number_format((float) $coupon->max_discount_amount, 2)
                                                    : 'No cap' }}
                                            </td>

                                            <td>
                                                {{ $coupon->used_count }} /
                                                {{ $coupon->usage_limit ?? '∞' }}
                                            </td>

                                            <td>
                                                {{ $coupon->starts_at?->format('d M Y H:i') ?? 'Any time' }}
                                            </td>

                                            <td>
                                                {{ $coupon->expires_at?->format('d M Y H:i') ?? 'No expiry' }}
                                            </td>

                                            <td>
                                                @if($coupon->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">

                                                    <a href="{{ route('coupons.show', $coupon->id) }}"
                                                    class="btn btn-sm btn-info text-white">
                                                        View
                                                    </a>

                                                    <a href="{{ route('coupons.edit', $coupon->id) }}"
                                                    class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>

                                                    <form method="POST"
                                                        action="{{ route('coupons.destroy', $coupon->id) }}"
                                                        onsubmit="return confirm('Are you sure you want to delete this coupon?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger">
                                                            Delete
                                                        </button>
                                                    </form>

                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                No coupons found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>

                        </div>

                        <div class="card-body">
                            {{ $coupons->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
