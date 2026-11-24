@extends('admin.layouts.app')

@section('title', 'Inventory')

@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-11">
                <h1 class="mb-0 fs-5">Inventory</h1>
              </div>
              <div class="col-sm-1">
                <a href="{{ url('/inventory/create') }}" class="btn btn-sm btn-primary" title="Add Inventory" style="float: right;">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                  </a>
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
                            <th>Product</th>
                            <th>Variant</th>
                            <th>On Hand</th>
                            <th>Reserved</th>
                            <th>Available</th>
                            <th>Reorder Level</th>
                            <th>Warehouse</th>
                            <th>Location</th>
                            <th>Last Restocked</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                  <tbody>
                    @if($inventories->isEmpty())
                      <tr>
                        <td colspan="12" class="text-center">No inventory found.</td>
                      </tr>
                    @endif
                    @foreach($inventories as $inventory)
                      <tr>
                        <td class="fs-7">{{ $loop->iteration }}.</td>
                        <td class="fs-7">{{ $inventory->product_name ?? 'N/A' }}</td>
                        <td class="fs-7">{{ $inventory->variant_name ?? 'N/A' }}</td>
                        <td class="fs-7">{{ $inventory->quantity_on_hand }}</td>
                        <td class="fs-7">{{ $inventory->reserved_quantity }}</td>
                        <td class="fs-7">{{ $inventory->available_quantity }}</td>
                        <td class="fs-7">{{ $inventory->reorder_level }}</td>
                        <td class="fs-7">{{ $inventory->warehouse_id ?? 'N/A' }}</td>
                        <td class="fs-7">{{ $inventory->location ?? 'N/A' }}</td>
                        <td class="fs-7">{{ date('d-m-Y H:i A', strtotime($inventory->last_restocked_at)) ?? 'N/A' }}</td>
                        <td class="fs-7">
                            <a href="{{ url('/inventory/' . $inventory->id . '/edit') }}" class="btn btn-sm btn-primary" title="Edit"><i class="fa fa-edit"></i></a>
                            <form action="{{ url('/inventory/' . $inventory->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
    </main>
    
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
