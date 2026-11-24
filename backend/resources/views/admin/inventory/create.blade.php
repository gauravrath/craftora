@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Add Inventory</h4>

    @if($errors->any())
        <div class="alert alert-danger">
            Please correct the errors below.
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form action="{{ route('inventory.store') }}"
                  method="POST">
                @csrf

                @include('admin.inventory._form')

            </form>

        </div>
    </div>

</div>
@endsection