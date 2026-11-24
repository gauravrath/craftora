@extends('admin.layouts.app')

@section('title', 'Products')
@section('styles')
	<style>
		.fs-5{
			font-size: 17px !important;
		}
		.preview-wrapper {
			max-height: 500px;
			max-width: 100%;
			overflow: auto;
			border: 1px solid #dee2e6;
		}

		#previewTable {
			min-width: 1800px;
			width: max-content;
			margin-bottom: 0;
			white-space: nowrap;
		}

		#previewTable th {
			position: sticky;
			top: 0;
			z-index: 2;
			background: #f8f9fa;
		}

		#previewTable th,
		#previewTable td {
			padding: 8px;
			vertical-align: middle;
		}

		#previewTable input {
			min-width: 150px;
		}
	</style>
@endsection
@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-9">
                <h4 class="mb-0 fs-5">Bulk Upload Products</h4>
              </div>
              <div class="col-sm-3">
                
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            <div class="card">
				<div class="card-header">
					<div class="d-flex align-items-center">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-4">
									<button class="btn btn-success btn-sm" onclick="window.location='{{ url('/product/bulk-upload/sample-download') }}'">Sample Download</button>
								</div>
								<div class="col-md-6">
									<input type="file" accept=".csv" id="bulkUploadFile">
									<button class="btn btn-primary btn-sm" id="uploadButton">Upload</button>
								</div>
								<div class="col-md-2">
									<div id="previewSectionBtn" style="display:none">
										<button type="button" class="btn btn-success" id="okBtn"> Save </button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
              	<div class="card-body">
                    <p class="text-muted">Create new products only. Separate image paths with semicolons and provide variants as a JSON array in the variants_json column. Image paths refer to files already stored on the server.</p>
                	<div id="message" class="mb-3" role="status"></div>
					<div id="previewSection" style="display:none">
						<div class="preview-wrapper">
							<table class="table table-bordered table-sm"
								id="previewTable">
								<thead>
									<tr id="tableHead"></tr>
								</thead>
								<tbody id="tableBody"></tbody>
							</table>
						</div>
					</div>                
            	</div>
            </div>
          </div>
        </div>
    </main>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/papaparse@5.4.1/papaparse.min.js"></script>
		<script src="{{ asset('dist/js/common.js') }}"></script>
    <script>
      $(function () {

          let headers = [];
          let products = [];

          const extraColumns = [
              'image_paths',
              'variants_json'
          ];

          const systemColumns = [
              'created_at',
              'updated_at',
              'deleted_at'
          ];

			function showMessage(message, type = 'info') {
				$('#message').stop(true, true).empty();

				const alertBox = $('<div>')
					.addClass('alert alert-' + type)
					.text(message)
					.appendTo('#message');

				alertBox.hide().fadeIn(200);

				setTimeout(function () {
					alertBox.fadeOut(300, function () {
						$(this).remove();
					});
				}, 5000);
			}

			function isAllowedColumn(header) {
				return /^[a-zA-Z_][a-zA-Z0-9_]*$/.test(header);
			}

          // Step 1: Parse CSV and create editable preview.
          $('#uploadButton').on('click', function () {

              const file = $('#bulkUploadFile')[0].files[0];

              if (!file) {
                  showMessage('Please select a CSV file.', 'warning');
                  return;
              }

              if (!file.name.toLowerCase().endsWith('.csv')) {
                  showMessage('Please upload a CSV file.', 'warning');
                  return;
              }

              if (file.size > 5 * 1024 * 1024) {
                  showMessage('Maximum file size is 5 MB.', 'warning');
                  return;
              }

              showMessage('Reading CSV file...', 'info');

              Papa.parse(file, {
                  header: true,
                  skipEmptyLines: 'greedy',
                  transformHeader: function (header) {
                      return header.trim();
                  },
                  complete: function (result) {

                      if (result.errors.length) {
                          showMessage(
                              'CSV parsing failed: ' +
                              result.errors[0].message,
                              'danger'
                          );
                          return;
                      }

                      headers = result.meta.fields || [];

                      if (!headers.length) {
                          showMessage(
                              'CSV headers could not be found.',
                              'danger'
                          );
                          return;
                      }

                      const duplicates = headers.filter(
                          (header, index) =>
                              headers.indexOf(header) !== index
                      );

                      if (duplicates.length) {
                          showMessage(
                              'Duplicate CSV column: ' +
                              duplicates[0],
                              'danger'
                          );
                          return;
                      }

                      if (headers.some(h => !isAllowedColumn(h))) {
                          showMessage(
                              'Invalid column name in CSV header.',
                              'danger'
                          );
                          return;
                      }

                      const requiredHeaders = [
                          'name',
                          'slug',
                          'price',
                          'stock_quantity'
                      ];

                      const missing = requiredHeaders.filter(
                          h => !headers.includes(h)
                      );

                      if (missing.length) {
                          showMessage(
                              'Missing required columns: ' +
                              missing.join(', '),
                              'danger'
                          );
                          return;
                      }

                      products = result.data.map(function (row) {
                          const cleanRow = {};

                          headers.forEach(function (header) {
                              cleanRow[header] =
                                  String(row[header] ?? '').trim();
                          });

                          return cleanRow;
                      });

                      renderPreview();

                      showMessage(
                          'CSV loaded. Review and edit the rows before saving.',
                          'success'
                      );
                  },
                  error: function () {
                      showMessage('Unable to read this CSV file.', 'danger');
                  }
              });
          });

          // Step 2: Render editable table cells.
          function renderPreview() {

              $('#tableHead').empty();
              $('#tableBody').empty();

              headers.forEach(function (header) {
                  $('<th>')
                      .text(header)
                      .appendTo('#tableHead');
              });

              $('<th>')
                  .text('Action')
                  .appendTo('#tableHead');

              products.forEach(function (product, rowIndex) {

                  const tr = $('<tr>').attr('data-row', rowIndex);

                  headers.forEach(function (header) {

                      const td = $('<td>');

                      const input = $('<input>')
                          .addClass('form-control form-control-sm')
                          .attr('type', 'text')
                          .attr('data-field', header)
                          .val(product[header] ?? '');

                      td.append(input);
                      tr.append(td);
                  });

                  const action = $('<td>');

                  $('<button>')
                      .attr('type', 'button')
                      .addClass('btn btn-outline-danger btn-sm delete-row')
                      .text('Remove')
                      .appendTo(action);

                  tr.append(action);

                  $('#tableBody').append(tr);
              });

              $('#rowCount').text(products.length);
              $('#previewSection').toggle(products.length > 0);
              $('#previewSectionBtn').toggle(products.length > 0);
          }

          // Keep edited input values synchronized.
          $('#tableBody').on('input', 'input[data-field]', function () {

              const rowIndex = Number($(this).closest('tr').attr('data-row'));
              const field = $(this).attr('data-field');

              products[rowIndex][field] = $(this).val();
          });

          // Optional: remove a row before saving.
          $('#tableBody').on('click', '.delete-row', function () {

              const rowIndex = Number($(this).closest('tr').attr('data-row'));

              products.splice(rowIndex, 1);
              renderPreview();

              showMessage('Row removed from the import preview.', 'info');
          });

          // Step 3: Send edited data to Laravel.
          $('#okBtn').on('click', function () {

              products = [];

              $('#tableBody tr').each(function () {

                  const product = {};

                  $(this).find('input[data-field]').each(function () {
                      product[$(this).attr('data-field')] = $(this).val();
                  });

                  products.push(product);
              });

              if (!products.length) {
                  showMessage('There are no rows to save.', 'warning');
                  return;
              }

              // Validate the main required values before the request.
              for (let i = 0; i < products.length; i++) {

                  const p = products[i];

                  if (!p.name || !p.slug ||
                      p.price === '' || p.stock_quantity === '') {

                      showMessage(
                          'Please complete Name, Slug, Price and Stock Quantity. ' +
                          'Check row ' + (i + 1) + '.',
                          'warning'
                      );

                      return;
                  }
              }

              if (!confirm(
                  'Save ' + products.length +
                  ' product rows to the database?'
              )) {
                  return;
              }

              const button = $('#okBtn');

              button.prop('disabled', true).text('Saving...');
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
              	$.ajax({
					url: "{{ route('products.import.save') }}",
					method: 'POST',
					contentType: 'application/json',
					data: JSON.stringify({
						products: products
					}),
					success: function (response) {
						showMessage(response.message, 'success');
						$('#previewSection').hide();
						$('#previewSectionBtn').hide();
						$('#bulkUploadFile').val('');
						products = [];
						headers = [];
					},
                  error: function (xhr) {
                      let message = 'Unable to save products.';

                      if (xhr.responseJSON) {

                          message = xhr.responseJSON.message || message;

                          if (xhr.responseJSON.errors) {
                              const errors = xhr.responseJSON.errors;

                              if (Array.isArray(errors)) {
                                  message += '\n' + errors.map(function (e) {
                                      return 'CSV row ' + e.row + ': ' +
                                          e.errors.join(', ');
                                  }).join('\n');
                              } else {
                                  message += '\n' +
                                      Object.values(errors).flat().join('\n');
                              }
                          }
                      }

                      showMessage(message, 'danger');
                  },
                  complete: function () {
                      button.prop('disabled', false)
                            .text('Save');
                  }
              });
          });

      });
    </script>
@endsection
