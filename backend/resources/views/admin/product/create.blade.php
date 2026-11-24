@extends('admin.layouts.app')

@section('title', 'Products')
@section('styles')
	<style>
		.input-size{
			height: 28px !important;
			border-radius: 0 !important;
			border-color: darkgray !important;
		}

		label{
			font-size: 0.875rem !important;
		}
	</style>
@endsection
@section('content')
<main class="app-main">
	<div class="app-content-header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6">
					<h1 class="mb-0 fs-5">Add Products</h1>
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
					<form action="{{ url('product/store') }}" method="POST" enctype="multipart/form-data">
						@csrf

						<div class="flexigrid col-md-12" style='width: 100%;'>
							<div class="formTabs">
								<div class="card-header bg-white" style="padding: 0;font-size: 14px;">
									<ul class="nav nav-tabs card-header-tabs float-left">
										<li class="nav-item">
											<a class="nav-link active" href="javascript:void(0);" id="prod_detail" onclick="productTabs('details')" style="color: black;">Product Detail</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="javascript:void(0);" id="prod_img" onclick="productTabs('image')"  style="color: black;">Product Image</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="javascript:void(0);" id="prod_var" onclick="productTabs('variants')"  style="color: black;">Product Variants</a>
										</li>

									</ul>
								</div>
								<div class="col-md-12 active" id="details" style="padding: 2%;">
									<div class="row">

										<div class="mb-3 col-sm-3">
											<label for="category_id" class="form-label">Category</label>
											<select class="form-select" id="category_id" name="category_id">
												<option value="">Select Category</option>
												@foreach($categories as $category)
													<option value="{{ $category->id }}">
														{{ $category->name }}
													</option>
												@endforeach
											</select>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="name" class="form-label">Name</label>
											<input type="text" class="form-control input-size"
												id="name" name="name" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="slug" class="form-label">Slug</label>
											<input type="text" class="form-control input-size"
												id="slug" name="slug">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="brand" class="form-label">Brand</label>
											<input type="text" class="form-control input-size"
												id="brand" name="brand">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="sku" class="form-label">SKU</label>
											<input type="text" class="form-control input-size"
												id="sku" name="sku">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="price" class="form-label">Price</label>
											<input type="number" class="form-control input-size"
												id="price" name="price" step="0.01" min="0" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="discount_price" class="form-label">Discount Price</label>
											<input type="number" class="form-control input-size"
												id="discount_price" name="discount_price" step="0.01" min="0">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="compare_price" class="form-label">Compare Price</label>
											<input type="number" class="form-control input-size"
												id="compare_price" name="compare_price" step="0.01" min="0">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="stock_quantity" class="form-label">Stock Quantity</label>
											<input type="number" class="form-control input-size"
												id="stock_quantity" name="stock_quantity"
												min="0" value="0" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="weight" class="form-label">Weight</label>
											<input type="number" class="form-control input-size"
												id="weight" name="weight" step="0.001" min="0">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="dimensions" class="form-label">Dimensions</label>
											<input type="text" class="form-control input-size"
												id="dimensions" name="dimensions">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="status" class="form-label">Status</label>
											<select class="form-select" id="status" name="status" required>
												<option value="draft" selected>Draft</option>
												<option value="published">Published</option>
												<option value="archived">Archived</option>
											</select>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="type" class="form-label">Product Type</label>
											<select class="form-select" id="type" name="type" required>
												<option value="physical" selected>Physical</option>
												<option value="digital">Digital</option>
											</select>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="sort_order" class="form-label">Sort Order</label>
											<input type="number" class="form-control input-size"
												id="sort_order" name="sort_order"
												min="0" value="0" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="is_featured" class="form-label">Featured</label>
											<select class="form-select" id="is_featured"
												name="is_featured" required>
												<option value="0" selected>No</option>
												<option value="1">Yes</option>
											</select>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="is_active" class="form-label">Active Status</label>
											<select class="form-select" id="is_active"
												name="is_active" required>
												<option value="1" selected>Active</option>
												<option value="0">Inactive</option>
											</select>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="video_urls" class="form-label">Video URLs (JSON)</label>
											<textarea class="form-control input-size" id="video_urls"
												name="video_urls" rows="2"
												placeholder='["https://example.com/video.mp4"]'></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="specifications" class="form-label">Specifications (JSON)</label>
											<textarea class="form-control input-size" id="specifications"
												name="specifications" rows="2"
												placeholder='{"color":"Black","size":"Large"}'></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="features" class="form-label">Features (JSON)</label>
											<textarea class="form-control input-size" id="features"
												name="features" rows="2"
												placeholder='["Durable","Lightweight"]'></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="short_description" class="form-label">Short Description</label>
											<textarea class="form-control input-size" id="short_description"
												name="short_description" rows="2"></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="description" class="form-label">Description</label>
											<textarea class="form-control input-size" id="description"
												name="description" rows="2"></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="seo_title" class="form-label">SEO Title</label>
											<input type="text" class="form-control input-size"
												id="seo_title" name="seo_title">
										</div>

										<div class="mb-3 col-sm-3">
											<label for="seo_description" class="form-label">SEO Description</label>
											<textarea class="form-control input-size" id="seo_description"
												name="seo_description" rows="2"></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="seo_keywords" class="form-label">SEO Keywords</label>
											<textarea class="form-control input-size" id="seo_keywords"
												name="seo_keywords" rows="2"></textarea>
										</div>

									</div>
								</div>
								<div class="col-md-12" id="image" style="padding: 2%; display: none;">
									<div class="row">
										<div class="mb-3 col-sm-3">
											<label for="image_path" class="form-label">Image Path</label>
											<input type="file" class="form-control input-size" id="image_path" name="image_path" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="image_url" class="form-label">Image URL</label>
											<input type="text" class="form-control input-size" id="image_url" name="image_url" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="alt_text" class="form-label">Alt Text</label>
											<input type="text" class="form-control input-size" id="alt_text" name="alt_text" required>
										</div>


										<div class="mb-3 col-sm-3">
											<label for="is_primary" class="form-label">Is Primary</label>
											<select class="form-select" id="is_primary" name="is_primary">
												<option value="">--Select--</option>
												<option value="1">Yes</option>
												<option value="0">No</option>
											</select>
										</div>

									</div>
								</div>
								<div class="col-md-12" id="variants" style="padding: 2%; display: none;">
									<div class="row">
										<div class="mb-3 col-sm-3">
											<label for="name" class="form-label">Name</label>
											<input type="file" class="form-control input-size" id="name" name="name" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="sku" class="form-label">SKU</label>
											<input type="text" class="form-control input-size" id="sku" name="sku" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="option_value" class="form-label">Option Value</label>
											<textarea  row="2" class="form-control input-size" id="option_value" name="option_value" required></textarea>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="price" class="form-label">Price</label>
											<input type="number" class="form-control input-size" id="price" name="price" required>
										</div>

										<div class="mb-3 col-sm-3">
											<label for="compare_price" class="form-label">Compare Price</label>
											<input type="number" class="form-control input-size" id="compare_price" name="compare_price" required>
										</div>
										<div class="mb-3 col-sm-3">
											<label for="stock" class="form-label">Stock Qty</label>
											<input type="text" class="form-control input-size" id="stock" name="stock" required>
										</div>
									</div>
								</div>
							</div>
						</div>

						<button type="submit" class="btn btn-primary">Save</button>
						<a href="{{ url('/product') }}" class="btn btn-secondary">Back</a>
					</form>
				</div>
			</div>
		</div>
	</div>
</main>

@endsection
@script('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="{{ asset('dist/js/scripts/product.js') }}"></script>
@endscript