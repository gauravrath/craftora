@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
    <main class="app-main">
        <div class="container-fluid py-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3>Order Details</h3>

                <a href="{{ route('admin.orders.index') }}"
                class="btn btn-secondary">
                    Back to Orders
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">

                {{-- Order information --}}
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <strong>Order Information</strong>
                        </div>

                        <div class="card-body">
                            <p>
                                <strong>Order Number:</strong>
                                {{ $order->order_number }}
                            </p>

                            <p>
                                <strong>Customer:</strong>
                                {{ $order->user?->name ?? 'Guest / Unknown' }}
                            </p>

                            <p>
                                <strong>Order Date:</strong>
                                {{ $order->created_at?->format('d M Y, h:i A') }}
                            </p>

                            <p>
                                <strong>Currency:</strong>
                                {{ $order->currency }}
                            </p>

                            <p>
                                <strong>Subtotal:</strong>
                                {{ number_format((float) $order->subtotal, 2) }}
                            </p>

                            <p>
                                <strong>Discount:</strong>
                                {{ number_format((float) $order->discount_amount, 2) }}
                            </p>

                            <p>
                                <strong>Shipping:</strong>
                                {{ number_format((float) $order->shipping_amount, 2) }}
                            </p>

                            <p>
                                <strong>Tax:</strong>
                                {{ number_format((float) $order->tax_amount, 2) }}
                            </p>

                            <hr>

                            <h5>
                                Total:
                                {{ $order->currency }}
                                {{ number_format((float) $order->total_amount, 2) }}
                            </h5>

                            <p class="mt-3 mb-0">
                                <strong>Notes:</strong>
                                {{ $order->notes ?: 'No notes' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Status update form --}}
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <strong>Update Order Status</strong>
                        </div>

                        <div class="card-body">

                            <form method="POST"
                                action="{{ route('admin.orders.updateStatus', $order->id) }}">

                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label class="form-label">Order Status</label>

                                    <select name="status" class="form-select" required>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}"
                                                @selected(old('status', $order->status) === $status)>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Status</label>

                                    <select name="payment_status"
                                            class="form-select"
                                            required>

                                        @foreach($paymentStatuses as $paymentStatus)
                                            <option value="{{ $paymentStatus }}"
                                                @selected(old('payment_status', $order->payment_status) === $paymentStatus)>
                                                {{ ucfirst($paymentStatus) }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <small class="text-muted">
                                        Payment status ko actual payment verification
                                        ke according hi update karein.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-success">
                                    Update Status
                                </button>

                            </form>

                        </div>
                    </div>
                </div>

                {{-- Ordered products --}}
                <div class="col-12">
                    <div class="card">

                        <div class="card-header">
                            <strong>Order Items</strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($order->items as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $item->product_name }}</td>

                                            <td>
                                                {{ $item->variant_name ?? 'N/A' }}
                                            </td>

                                            <td>{{ $item->quantity }}</td>

                                            <td>
                                                {{ $order->currency }}
                                                {{ number_format((float) $item->unit_price, 2) }}
                                            </td>

                                            <td>
                                                {{ $order->currency }}
                                                {{ number_format((float) $item->total_price, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                No order items found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
    
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
