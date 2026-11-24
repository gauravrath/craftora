@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-11">
                <h1 class="mb-0 fs-5">Orders</h1>
              </div>
              <div class="col-sm-1">
               
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            <div class="card">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Shipping</th>
                                <th>Tax</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        {{ $orders->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $order->order_number }}
                                    </td>

                                    <td>
                                        {{ $order->user?->name ?? 'Guest / Unknown' }}
                                    </td>

                                    <td>{{ $order->items_count }}</td>

                                    <td>
                                        {{ $order->currency }}
                                        {{ number_format((float) $order->subtotal, 2) }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $order->discount_amount, 2) }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $order->shipping_amount, 2) }}
                                    </td>

                                    <td>
                                        {{ number_format((float) $order->tax_amount, 2) }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $order->currency }}
                                            {{ number_format((float) $order->total_amount, 2) }}
                                        </strong>
                                    </td>

                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge
                                            {{ $order->payment_status === 'paid'
                                                ? 'bg-success'
                                                : 'bg-warning text-dark' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $order->created_at?->format('d M Y, h:i A') }}
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <a href="{{ route('orders.show', $order->id) }}"
                                            class="btn btn-sm btn-primary">
                                                View
                                            </a>

                                            <form method="POST"
                                                action="{{ route('orders.destroy', $order->id) }}"
                                                onsubmit="return confirm('Move this order to deleted records?')">
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
                                    <td colspan="13" class="text-center py-4">
                                        No orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>
    </main>
    
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
