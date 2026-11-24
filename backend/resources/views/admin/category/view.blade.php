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
					<a href="{{ route('category.edit', $category->id) }}"  class="btn btn-sm btn-outline-secondary">Edit</a>
				</div>
			</div>
		</div>
	</div>
	<div class="app-content">
		<div class="container-fluid">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Categories</h3>


				</div>
				<div class="card-body">
					<form>
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="name" class="form-label">Name</label>
									<input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" readonly>
								</div>
								<div class="mb-3 col-sm-6">
									<label for="slug" class="form-label">Slug</label>
									<input type="text" class="form-control" id="slug" name="slug" value="{{ $category->slug }}" readonly>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="description" class="form-label">Description</label>
									<textarea class="form-control" id="description" name="description" readonly>{{ $category->description }}</textarea>
								</div>
								<div class="mb-3 col-sm-6">
									<label for="is_active" class="form-label">Is Active</label>
									<select class="form-select" id="is_active" name="is_active" disabled>
										<option value="1" {{ $category->is_active ? 'selected' : '' }}>Yes</option>
										<option value="0" {{ !$category->is_active ? 'selected' : '' }}>No</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="image_url" class="form-label">Image URL</label>
									<input type="file" class="form-control" id="image_url" name="image_url" disabled>
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