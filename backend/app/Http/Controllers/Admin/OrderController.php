<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Orders listing, search and filters.
     */
    public function index(Request $request)
    {
        $query = Order::query()
            ->with('user')
            ->withCount('items');

        // Search by order number
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where('order_number', 'like', "%{$search}%");
        }

        // Filter by order status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where(
                'payment_status',
                $request->payment_status
            );
        }

        $orders = $query
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $statuses = [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
        ];

        $paymentStatuses = [
            'pending',
            'paid',
            'failed',
            'refunded',
        ];

        return view('admin.order.index', compact(
            'orders',
            'statuses',
            'paymentStatuses'
        ));
    }

    /**
     * Show one order with its items.
     */
    public function show(string $id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'items.variant',
        ])->findOrFail($id);

        $statuses = [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
        ];

        $paymentStatuses = [
            'pending',
            'paid',
            'failed',
            'refunded',
        ];

        return view('admin.order.show', compact(
            'order',
            'statuses',
            'paymentStatuses'
        ));
    }

    /**
     * Update order and payment status.
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                    'cancelled',
                ]),
            ],

            'payment_status' => [
                'required',
                Rule::in([
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                ]),
            ],
        ]);

        
        if (
            $order->status === 'cancelled' &&
            $validated['status'] !== 'cancelled'
        ) {
            return back()->withErrors([
                'status' => 'Cancelled order ko reopen karne ke liye '
                    . 'separate process implement karein.',
            ]);
        }

        $order->update($validated);

        return redirect()
            ->route('orders.show', $order->id)
            ->with('success', 'Order status updated successfully.');
    }

    /**
     * Soft delete an order.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order moved to deleted records.');
    }
}