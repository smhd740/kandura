@extends('layouts.admin')

@section('title', __('Orders Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-shopping-cart"></i> {{ __('Orders Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View and manage all orders') }}</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <a href="{{ route('admin.orders.statistics') }}" class="btn btn-info">
                    <i class="ti ti-chart-bar"></i> {{ __('Statistics') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-primary">{{ $stats['total_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['total_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Pending') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-yellow">{{ $stats['pending_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['pending_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Processing') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-cyan">{{ $stats['processing_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['processing_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Revenue') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-success">${{ number_format($stats['total_revenue'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('Order number, user name...') }}"
                           value="{{ request('search') }}">
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Payment Method -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Payment Method') }}</label>
                    <select name="payment_method" class="form-select">
                        <option value="">{{ __('All Methods') }}</option>
                        <option value="stripe" {{ request('payment_method') == 'stripe' ? 'selected' : '' }}>{{ __('Stripe') }}</option>
                        <option value="wallet" {{ request('payment_method') == 'wallet' ? 'selected' : '' }}>{{ __('Wallet') }}</option>
                        <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>{{ __('Cash on Delivery') }}</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Payment Status') }}</label>
                    <select name="payment_status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    </select>
                </div>

                <!-- Date From -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <!-- Date To -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                <!-- Price From -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Price From') }}</label>
                    <input type="number" name="price_from" class="form-control" step="0.01"
                           placeholder="0.00" value="{{ request('price_from') }}">
                </div>

                <!-- Price To -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Price To') }}</label>
                    <input type="number" name="price_to" class="form-control" step="0.01"
                           placeholder="0.00" value="{{ request('price_to') }}">
                </div>

                <!-- Submit -->
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'status', 'payment_method', 'payment_status', 'date_from', 'date_to', 'price_from', 'price_to']))
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Orders') }} ({{ $orders->total() }})</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Order') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th class="w-1">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <!-- Order Number -->
                            <td>
                                <div class="fw-bold">{{ $order->order_number }}</div>
                                <div class="text-muted small">ID: {{ $order->id }}</div>
                            </td>

                            <!-- Customer -->
                            <td>
                                <div>{{ $order->user->name }}</div>
                                <div class="text-muted small">{{ $order->user->email }}</div>
                            </td>

                            <!-- Items Count -->
                            <td>
                                <span class="badge bg-info">
                                    {{ $order->items->count() }} {{ __('items') }}
                                </span>
                            </td>

                            <!-- Total -->
                            <td>
                                <div class="fw-bold">${{ number_format($order->total_amount, 2) }}</div>
                                @if($order->discount_amount > 0)
                                    <div class="text-muted small">
                                        <i class="ti ti-discount"></i> -${{ number_format($order->discount_amount, 2) }}
                                    </div>
                                @endif
                            </td>

                            <!-- Order Status -->
                            <td>
                                <span class="badge bg-{{
                                    $order->status == 'completed' ? 'success' :
                                    ($order->status == 'processing' ? 'cyan' :
                                    ($order->status == 'cancelled' ? 'danger' : 'yellow'))
                                }}">
                                    {{ __(ucfirst($order->status)) }}
                                </span>
                            </td>

                            <!-- Payment Info -->
                            <td>
                                <div>
                                    <span class="badge bg-{{
                                        $order->payment_method == 'stripe' ? 'blue' :
                                        ($order->payment_method == 'wallet' ? 'purple' : 'orange')
                                    }}">
                                        {{ __(ucfirst($order->payment_method)) }}
                                    </span>
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ __(ucfirst($order->payment_status)) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div>{{ $order->created_at->format('Y-m-d') }}</div>
                                <div class="text-muted small">{{ $order->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ __('View Details') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-shopping-cart"></i>
                                    </div>
                                    <p class="empty-title">{{ __('No orders found') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('Orders will appear here once customers place them.') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                {{ __('Showing') }} {{ $orders->firstItem() }} {{ __('to') }} {{ $orders->lastItem() }}
                {{ __('of') }} {{ $orders->total() }} {{ __('entries') }}
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $orders->links() }}
            </ul>
        </div>
        @endif
    </div>
@endsection
