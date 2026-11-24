@extends('admin.layouts.app')

@section('title', 'Categories')
@section('content')
@section('styles')
	<style>
		label{
			font-size: 15px;
		}
		.input-size{
			height: 28px !important;
			border-radius: 0 !important;
			border-color: darkgray !important;
		}
	</style>
@endsection
<main class="app-main">
	<div class="app-content-header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6">
					<h1 class="mb-0 fs-5">Categories</h1>
				</div>
				<div class="col-sm-6">
					
				</div>
			</div>
		</div>
	</div>
	<div class="app-content">
		<div class="container-fluid">
			<div class="card">
				<div class="card-body">
					<form action="{{ url('category/store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="name" class="form-label">Name</label>
									<input type="text" class="form-control input-size" id="name" name="name" required>
								</div>
								<div class="mb-3 col-sm-6">
									<label for="slug" class="form-label">Slug</label>
									<input type="text" class="form-control input-size" id="slug" name="slug">
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="description" class="form-label">Description</label>
									<textarea class="form-control input-size" id="description" name="description"></textarea>
								</div>
								<div class="mb-3 col-sm-6">
									<label for="is_active" class="form-label input-size">Is Active</label>
									<select class="form-select" id="is_active" name="is_active" required>
										<option value="1">Yes</option>
										<option value="0">No</option>
									</select>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="mb-3 col-sm-6">
									<label for="image_url" class="form-label">Image URL</label>
									<input type="file" class="form-control input-size" id="image_url" name="image_url">
								</div>
							</div>
						</div>
						<button type="submit" class="btn btn-primary">Save</button>
						<a href="{{ url('/category') }}" class="btn btn-secondary">Back</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</main>

<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection