<?php

namespace App\Http\Controllers\AdminUI;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address.city', 'items.design'])
            ->latest();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('price_from') || $request->filled('price_to')) {
            $query->priceRange($request->price_from, $request->price_to);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->sort($sortBy, $sortOrder);

        $orders = $query->paginate(15);

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::byStatus('pending')->count(),
            'processing_orders' => Order::byStatus('processing')->count(),
            'completed_orders' => Order::byStatus('completed')->count(),
            'cancelled_orders' => Order::byStatus('cancelled')->count(),
            'total_revenue' => Order::byStatus('completed')->sum('total_amount'),
            'pending_payments' => Order::where('payment_status', 'pending')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'address.city',
            'items.design.images',
            'items.measurements',
            'items.designOptions',
            'coupon',
            'transactions',
            'review.user'
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        if (!$order->canUpdateStatus() && $request->status !== 'cancelled') {
            return redirect()->back()->with('error', __('Cannot update status for this order.'));
        }

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', __('Order status updated successfully.'));
    }

    public function markAsPaid(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('error', __('Order is already marked as paid.'));
        }

        if ($order->payment_method !== 'cod') {
            return redirect()->back()->with('error', __('Only COD orders can be manually marked as paid.'));
        }

        $order->update([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', __('Order marked as paid successfully.'));
    }

    public function statistics()
{
    $stats = [
        'total_orders' => Order::count(),
        'pending_orders' => Order::byStatus('pending')->count(),
        'processing_orders' => Order::byStatus('processing')->count(),
        'completed_orders' => Order::byStatus('completed')->count(),
        'cancelled_orders' => Order::byStatus('cancelled')->count(),
        'total_revenue' => Order::byStatus('completed')->sum('total_amount'),
        'average_order_value' => Order::byStatus('completed')->avg('total_amount'),
    ];

    return view('admin.orders.statistics', compact('stats'));
}
}
