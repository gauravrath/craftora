<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomersController extends Controller
{
     public function index(Request $request)
    {
        $query = User::query()
            ->withCount('addresses');

        // Search customer
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                // Phone column exists hone par hi search karein.
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                    $q->orWhere('phone', 'like', "%{$search}%");
                }
            });
        }

        // Optional active/inactive filter
        if (
            $request->filled('status') &&
            \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active')
        ) {
            $query->where('is_active', $request->status);
        }

        $customers = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customer.index', compact('customers'));
    }

    /**
     * One customer and all their addresses.
     */
    public function show($id)
    {
        $customer = User::with([
            'addresses' => function ($query) {
                $query->orderByDesc('is_default')
                    ->orderByDesc('created_at');
            },
        ])->findOrFail($id);

        return view('admin.customer.show', compact('customer'));
    }

    /**
     * Activate/deactivate customer.
     * Requires users.is_active column.
     */
    public function toggleStatus($id)
    {
        abort_unless(
            \Illuminate\Support\Facades\Schema::hasColumn('users', 'is_active'),
            500,
            'Please add is_active column to the users table first.'
        );

        $customer = User::findOrFail($id);

        $customer->is_active = !$customer->is_active;
        $customer->save();

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer status updated successfully.');
    }
}
