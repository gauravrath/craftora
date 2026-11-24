@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Customer Details</h3>

        <a href="{{ route('customers.index') }}"
           class="btn btn-secondary">
            Back to Customers
        </a>
    </div>

    {{-- Customer profile --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Customer Information</strong>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <strong>Customer ID</strong>
                    <p>{{ $customer->id }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Name</strong>
                    <p>{{ $customer->name }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Email</strong>
                    <p>{{ $customer->email }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Phone</strong>
                    <p>{{ $customer->phone ?? 'N/A' }}</p>
                </div>

                <div class="col-md-4">
                    <strong>Registered At</strong>
                    <p>
                        {{ $customer->created_at?->format('d M Y, h:i A') ?? 'N/A' }}
                    </p>
                </div>

                <div class="col-md-4">
                    <strong>Total Saved Addresses</strong>
                    <p>{{ $customer->addresses->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer addresses --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <strong>Customer Addresses</strong>
            <span class="badge bg-primary">
                {{ $customer->addresses->count() }} Addresses
            </span>
        </div>

        <div class="card-body">
            @forelse($customer->addresses as $address)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <h5>{{ $address->label ?: 'Address' }}</h5>

                        @if($address->is_default)
                            <span class="badge bg-primary">
                                Default Address
                            </span>
                        @endif
                    </div>

                    <p class="mb-1">
                        <strong>Name:</strong> {{ $address->full_name }}
                    </p>

                    <p class="mb-1">
                        <strong>Phone:</strong> {{ $address->phone }}
                    </p>

                    <p class="mb-1">
                        <strong>Address:</strong>
                        {{ $address->address_line_1 }}

                        @if($address->address_line_2)
                            , {{ $address->address_line_2 }}
                        @endif
                    </p>

                    <p class="mb-1">
                        <strong>City:</strong> {{ $address->city }}
                    </p>

                    <p class="mb-1">
                        <strong>State:</strong> {{ $address->state }}
                    </p>

                    <p class="mb-1">
                        <strong>Postal Code:</strong> {{ $address->postal_code }}
                    </p>

                    <p class="mb-1">
                        <strong>Country:</strong> {{ $address->country }}
                        ({{ $address->country_code ?? 'N/A' }})
                    </p>

                    <p class="mb-1">
                        <strong>Coordinates:</strong>
                        {{ $address->latitude ?? 'N/A' }},
                        {{ $address->longitude ?? 'N/A' }}
                    </p>

                    <p class="mb-0">
                        <strong>Notes:</strong>
                        {{ $address->notes ?: 'N/A' }}
                    </p>
                </div>
            @empty
                <p class="text-muted mb-0">
                    This customer has no saved addresses.
                </p>
            @endforelse
        </div>
    </div>

</div>
    
    <script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
