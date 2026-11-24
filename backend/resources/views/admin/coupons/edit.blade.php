@extends('admin.layouts.app')

@section('title', 'Coupons')

@section('content')
 <main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <h3 class="mb-4">Edit Coupon</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    Please correct the errors below.
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <strong>Edit: {{ $coupon->code }}</strong>
                </div>

                <div class="card-body">
                    <form method="POST"
                        action="{{ route('coupons.update', $coupon->id) }}">
                        @csrf
                        @method('PUT')

                        @include('admin.coupons._form', ['coupon' => $coupon])
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
    
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
