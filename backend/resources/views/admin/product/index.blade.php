@extends('admin.layouts.app')

@section('title', 'Products')

@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-9">
                <h1 class="mb-0 fs-5">Products</h1>
              </div>
              <div class="col-sm-3" style="float: right;">
                <button class="btn btn-success btn-sm" onclick="window.location='{{ url('/product/bulk-upload') }}'">Bulk Upload Products</button>
                <a href="{{ url('/product/create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus me-1" aria-hidden="true"></i>
                    Add
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
                      <th class="fs-7">S.No</th>
                      <th class="fs-7">Category</th>
                      <th class="fs-7">Name</th>
                      <th class="fs-7">Slug</th>
                      <th class="fs-7">Brand</th>
                      <th class="fs-7">Price</th>
                      <th class="fs-7">Stock Quantity</th>
                      <th class="fs-7">Active</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if($products->isEmpty())
                      <tr>
                        <td colspan="8" class="text-center">No products found.</td>
                      </tr>
                    @endif
                    @foreach($products as $product)
                      <tr>
                        <td class="fs-7">{{ $loop->iteration }}.</td>
                        <td class="fs-7">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="fs-7"><a href="{{ url('/product/' . $product->id) }}">{{ $product->name }}</a></td>
                        <td class="fs-7">{{ $product->slug }}</td>
                        <td class="fs-7">{{ $product->brand }}</td>
                        <td class="fs-7">{{ $product->price }}</td>
                        <td class="fs-7">{{ $product->stock_quantity }}</td>
                        <td class="fs-7">
                          <button type="button" class="btn btn-sm @if($product->is_active) btn-outline-success @else btn-outline-danger @endif" onclick="changeStatus('{{ $product->id }}')">
                            @if($product->is_active)
                              Active
                            @else
                              Inactive
                            @endif
                          </button>
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
    <script>
      function changeStatus(id) {
        fetch(`{{ url('/product') }}/${id}/status`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        }).then(response => {
          if (!response.ok) {
            throw new Error('Failed to update product status.');
          }

          window.location.reload();
        }).catch(error => {
          alert(error.message);
        });
      }
    </script>
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
