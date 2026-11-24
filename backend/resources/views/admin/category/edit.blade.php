@extends('admin.layouts.app')

@section('title', 'Categories')
@section('styles')
	<style>
		label{
			font-size: 12px;
		}
	</style>
@endsection
@section('content')
<main class="app-main">
	<div class="app-content-header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-11">
					<h1 class="mb-0 fs-3">Categories</h1>
				</div>
				<div class="col-sm-1">
					
				</div>
			</div>
		</div>
	</div>
	<div class="app-content">
		<div class="container-fluid">
			<div class="card">
				<div class="card-body">
					@if($errors->any())
						<div class="alert alert-danger" role="alert">
							<ul class="mb-0">
								@foreach($errors->all() as $error)
									<li>{{ $error }}</li>
								@endforeach
							</ul>
						</div>
					@endif
					<form method="POST" action="{{ route('category.update', $category) }}" enctype="multipart/form-data">
						@csrf
						@method('PUT')
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="name" class="form-label">Name</label>
									<input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" >
								</div>
								<div class="mb-3 col-sm-6">
									<label for="slug" class="form-label">Slug</label>
									<input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" >
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="description" class="form-label">Description</label>
									<textarea class="form-control" id="description" name="description" >{{ old('description', $category->description) }}</textarea>
								</div>
								<div class="mb-3 col-sm-6">
									<label for="is_active" class="form-label">Is Active</label>
									<select class="form-select" id="is_active" name="is_active">
										<option value="1" {{ old('is_active', (int) $category->is_active) == 1 ? 'selected' : '' }}>Yes</option>
										<option value="0" {{ old('is_active', (int) $category->is_active) == 0 ? 'selected' : '' }}>No</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="image_url" class="form-label">Image</label>
									<input type="file" class="form-control" id="image_url" name="image_url" accept=".jpg,.jpeg,.png,.webp,.mp4,.mov,.webm">
									<small class="form-text text-muted">Leave empty to keep the current image. If validation fails, select the file again.</small>
								</div>
								<div class="mb-3 col-sm-6">
									@if($image)
										<img src="{{ $image }}" width="300" alt="{{ $category->name }}">
									@else
										<span class="text-muted">No image</span>
									@endif
								</div>
							</div>
						</div>
						<button type="submit" class="btn btn-secondary">Update</button>
						<a href="{{ url('/category') }}" class="btn btn-secondary">Back</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</main>

@script('scripts')
<script src="{{ asset('dist/js/common.js') }}"></script>
@endscript
@endsection