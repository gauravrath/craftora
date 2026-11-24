@extends('admin.layouts.app')

@section('title', 'Customers')

@section('content')
 <main class="app-main">
        <div class="app-content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-11">
                <h1 class="mb-0 fs-5">Customers</h1>
              </div>
              <div class="col-sm-1">
               
              </div>
            </div>
          </div>
        </div>
        <div class="app-content">
          <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('customers.index') }}">

                        <div class="row g-3">
                            <div class="col-md-6">  
                                <input type="text"
                                    name="search"
                                    class="form-control"
                                    placeholder="Search name, email or phone"
                                    value="{{ request('search') }}">
                            </div>

                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="1"
                                        @selected(request('status') === '1')>
                                        Active
                                    </option>
                                    <option value="0"
                                        @selected(request('status') === '0')>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-primary" type="submit">
                                    Search
                                </button>

                                <a href="{{ route('customers.index') }}"
                                class="btn btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
              <div class="col-sm-12">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Addresses</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                  <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>
                                {{ $customers->firstItem() + $loop->index }}
                            </td>

                            <td>{{ $customer->name }}</td>

                            <td>{{ $customer->email }}</td>

                            <td>{{ $customer->phone ?? 'N/A' }}</td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $customer->addresses_count }}
                                </span>
                            </td>

                            <td>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active'))
                                    @if($customer->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                @else
                                    <span class="text-muted">Not configured</span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                       class="btn btn-sm btn-info text-white">
                                        View Details
                                    </a>

                                    @if(\Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active'))
                                        <form method="POST"
                                              action="{{ route('customers.toggleStatus', $customer->id) }}"
                                              onsubmit="return confirm('Change customer status?')">
                                            @csrf
                                            @method('PATCH')

                                            @if($customer->is_active)
                                                <button class="btn btn-sm btn-danger"
                                                        type="submit">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-success"
                                                        type="submit">
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
              <div class="card-body">
                {{ $customers->links() }}
            </div>
            </div>
          </div>
        </div>
    </main>
    
		<script src="{{ asset('dist/js/common.js') }}"></script>
@endsection
