@extends('admin.layouts.app')

@section('title', 'Categories')
@section('styles')
<style>
  .text-decoration-none {
    text-decoration: none;
  }
</style>
@endsection
@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-11">
                <h1 class="mb-0 fs-5">Categories</h1>
              </div>
              <div class="col-sm-1">
                <a href="{{ route('category.create') }}" class="btn btn-sm btn-outline-secondary">
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
                      <th class="fs-7">Name</th>
                      <th class="fs-7">Slug</th>
                      <th class="fs-7">Image</th>
                      <th class="fs-7">Description</th>
                      <th class="fs-7">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if($categories->isEmpty())
                      <tr>
                        <td colspan="6" class="text-center">No categories found.</td>
                      </tr>
                    @endif
                    @foreach($categories as $category)
                      <tr>
                        <td class="fs-7">{{ $loop->iteration }}.</td>
                        <td class="fs-7"><a href="{{ route('category.view', $category->id) }}" class="text-decoration-none">{{ $category->name }}</a></td>
                        <td class="fs-7">{{ $category->slug }}</td>
                        <td class="fs-7">
                          @if($image = $category->imagePreviewUrl())
                            <img src="{{ $image }}" alt="{{ $category->name }}" style="width: 50px; height: 50px;">
                          @else
                            <span class="text-muted">No image</span>
                          @endif
                        </td>
                        <td class="fs-7" title="{{ $category->description }}">{{ Str::limit($category->description, 50, '...') }}</td>
                        <td class="fs-7">
                          <button type="button" class="btn btn-sm @if($category->is_active) btn-outline-success @else btn-outline-danger @endif" onclick="changeStatus({{ $category->id }})">
                            @if($category->is_active)
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
        {{ $categories->links() }}
    </main>
    <script>
      function changeStatus(id) {
        fetch(`{{ url('/category') }}/${id}/status`, {
          method: 'PATCH',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        }).then(response => {
          if (!response.ok) {
            throw new Error('Failed to update category status.');
          }

          window.location.reload();
        }).catch(error => {
          alert(error.message);
        });
      }
    </script>
		<script src="{{ asset('dist/js/common.js') }}"></script>

@endsection
