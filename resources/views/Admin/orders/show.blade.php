@extends('layouts.admin')

@section('title', __('Order Details') . ' - ' . $order->order_number)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.orders.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Orders') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-shopping-cart"></i> {{ __('Order') }} #{{ $order->order_number }}
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @if($order->payment_method === 'cod' && $order->payment_status === 'pending')
                <form method="POST" action="{{ route('admin.orders.mark-paid', $order) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-cash"></i> {{ __('Mark as Paid') }}
                    </button>
                </form>
                @endif

                @if($order->canUpdateStatus())
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="ti ti-edit"></i> {{ __('Update Status') }}
                </button>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Order Items -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-package"></i> {{ __('Order Items') }}
            </h3>
        </div>
        <div class="card-body p-0">
            @foreach($order->items as $item)
                <div class="p-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="row g-4">
                        <!-- Image -->
                        <div class="col-auto">
                            @if($item->design->images->first())
                                <img src="{{ asset('storage/' . $item->design->images->first()->image_path) }}"
                                     alt="{{ $item->design->name }}"
                                     class="rounded"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center rounded bg-light"
                                     style="width: 120px; height: 120px;">
                                    <i class="ti ti-shirt fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Design Info & Details -->
                        <div class="col">
                            <h4 class="mb-2">{{ $item->design->name }}</h4>
                            <p class="text-muted mb-3">{{ $item->design->description }}</p>

                            <div class="row g-3">
                                <!-- Sizes -->
                                @if($item->measurements->count() > 0)
                                <div class="col-md-6">
                                    <div class="mb-1"><strong class="text-muted">{{ __('Sizes') }}:</strong></div>
                                    @foreach($item->measurements as $measurement)
                                        <span class="badge bg-info me-1">{{ $measurement->name }}</span>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Options -->
                                @if($item->designOptions->count() > 0)
                                <div class="col-md-6">
                                    <div class="mb-1"><strong class="text-muted">{{ __('Options') }}:</strong></div>
                                    @foreach($item->designOptions as $option)
                                        <span class="badge bg-primary me-1 mb-1">
                                            {{ __($option->type) }}: {{ $option->name }}
                                        </span>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Quantity & Price -->
                                <div class="col-12">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="badge bg-secondary badge-lg">
                                                <i class="ti ti-package me-1"></i>
                                                {{ __('Quantity') }}: {{ $item->quantity }}
                                            </span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="text-muted">${{ number_format($item->unit_price, 2) }} {{ __('each') }}</span>
                                        </div>
                                        <div class="col text-end">
                                            <span class="h3 mb-0 text-primary">${{ number_format($item->subtotal, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Totals -->
            <div class="p-4 bg-light">
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-end text-muted">{{ __('Subtotal') }}:</td>
                                <td class="text-end fw-bold" style="width: 150px;">
                                    ${{ number_format($order->subtotal ?? $order->total_amount, 2) }}
                                </td>
                            </tr>

                            @if($order->discount_amount > 0)
                            <tr>
                                <td class="text-end text-success">
                                    {{ __('Discount') }}
                                    @if($order->coupon)
                                        <span class="badge bg-success ms-1">{{ $order->coupon->code }}</span>
                                    @endif
                                </td>
                                <td class="text-end text-success fw-bold">
                                    -${{ number_format($order->discount_amount, 2) }}
                                </td>
                            </tr>
                            @endif

                            <tr class="border-top">
                                <td class="text-end pt-3"><h3 class="mb-0">{{ __('Total') }}:</h3></td>
                                <td class="text-end pt-3"><h3 class="mb-0 text-primary">${{ number_format($order->total_amount, 2) }}</h3></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Column Layout -->
    <div class="row row-deck row-cards">
        <!-- Shipping Address -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin"></i> {{ __('Shipping Address') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if($order->address)
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('Name') }}</div>
                            <div class="fw-bold">{{ $order->address->name }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('City') }}</div>
                            <div class="fw-bold">{{ $order->address->city->name ?? __('Unknown') }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('Street') }}</div>
                            <div class="fw-bold">{{ $order->address->street }}</div>
                        </div>
                        @if($order->address->building_number)
                        <div class="mb-3">
                            <div class="text-muted small mb-1">{{ __('Building') }}</div>
                            <div class="fw-bold">{{ $order->address->building_number }}</div>
                        </div>
                        @endif
                        @if($order->address->details)
                        <div>
                            <div class="text-muted small mb-1">{{ __('Details') }}</div>
                            <div class="fw-bold">{{ $order->address->details }}</div>
                        </div>
                        @endif
                    @else
                        <p class="text-muted mb-0">{{ __('No address provided') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status & Payment -->
        <div class="col-lg-4">
            <!-- Order Status -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Order Status') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small mb-2">{{ __('Current Status') }}</div>
                        <span class="badge bg-{{
                            $order->status == 'completed' ? 'success' :
                            ($order->status == 'processing' ? 'cyan' :
                            ($order->status == 'cancelled' ? 'danger' : 'yellow'))
                        }} badge-lg">
                            {{ __(ucfirst($order->status)) }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">{{ __('Order Date') }}</div>
                        <div class="fw-bold">{{ $order->created_at->format('Y-m-d H:i') }}</div>
                        <div class="text-muted small">{{ $order->created_at->diffForHumans() }}</div>
                    </div>

                    @if($order->paid_at)
                    <div>
                        <div class="text-muted small mb-1">{{ __('Paid At') }}</div>
                        <div class="fw-bold">{{ $order->paid_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Payment') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small mb-2">{{ __('Method') }}</div>
                        <span class="badge bg-{{
                            $order->payment_method == 'stripe' ? 'blue' :
                            ($order->payment_method == 'wallet' ? 'purple' : 'orange')
                        }} badge-lg">
                            {{ __(ucfirst($order->payment_method)) }}
                        </span>
                    </div>

                    <div>
                        <div class="text-muted small mb-2">{{ __('Status') }}</div>
                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} badge-lg">
                            {{ __(ucfirst($order->payment_status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Customer') }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3 pb-3 border-bottom">
                        <span class="avatar avatar-xl mb-2">{{ strtoupper(substr($order->user->name, 0, 2)) }}</span>
                        <h4 class="mb-0">
                            <a href="{{ route('admin.users.show', $order->user) }}">
                                {{ $order->user->name }}
                            </a>
                        </h4>
                    </div>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">{{ __('Email') }}</div>
                        <div><a href="mailto:{{ $order->user->email }}">{{ $order->user->email }}</a></div>
                    </div>

                    <div>
                        <div class="text-muted small mb-1">{{ __('Phone') }}</div>
                        <div><a href="tel:{{ $order->user->phone }}">{{ $order->user->phone }}</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Information -->
    @if($order->invoice)
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-file-invoice"></i> {{ __('Invoice Information') }}
            </h3>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small mb-1">{{ __('Invoice Number') }}</div>
                            <div class="fw-bold">{{ $order->invoice->invoice_number }}</div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small mb-1">{{ __('Total') }}</div>
                            <div class="fw-bold">${{ number_format($order->invoice->total, 2) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small mb-1">{{ __('Generated') }}</div>
                            <div class="fw-bold">{{ $order->invoice->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-list">
                        <a href="{{ route('admin.invoices.view', $order->invoice->id) }}"
                           class="btn btn-info"
                           target="_blank">
                            <i class="ti ti-eye"></i> {{ __('View PDF') }}
                        </a>
                        <a href="{{ route('admin.invoices.download', $order->invoice->id) }}"
                           class="btn btn-primary">
                            <i class="ti ti-download"></i> {{ __('Download PDF') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif


             <!-- Customer Review -->
    @if($order->status === 'completed')
    <div class="card mt-3">
        <div class="card-header bg-light">
            <h3 class="card-title">
                <i class="ti ti-star text-warning"></i> {{ __('Customer Review') }}
            </h3>
        </div>
        @if($order->review)
        <div class="card-body">
            <div class="d-flex align-items-start mb-3">
                <!-- Avatar & Info -->
                <span class="avatar avatar-md me-3 flex-shrink-0">
                    {{ strtoupper(substr($order->review->user->name, 0, 2)) }}
                </span>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <div class="fw-bold me-2">{{ $order->review->user->name }}</div>
                        <div class="ms-auto">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $order->review->rating)
                                    <i class="ti ti-star-filled text-warning"></i>
                                @else
                                    <i class="ti ti-star text-muted"></i>
                                @endif
                            @endfor
                            <span class="badge bg-warning-lt ms-2">{{ $order->review->rating }}/5</span>
                        </div>
                    </div>
                    <div class="text-muted small mb-2">
                        <i class="ti ti-clock me-1"></i>
                        {{ $order->review->created_at->diffForHumans() }}
                    </div>
                    @if($order->review->comment)
                    <div class="text-secondary mt-3">
                        {{ $order->review->comment }}
                    </div>
                    @else
                    <div class="text-muted fst-italic mt-3">
                        {{ __('No comment provided') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="card-body text-center py-5 text-muted">
            <i class="ti ti-star-off" style="font-size: 3rem; opacity: 0.3;"></i>
            <div class="mt-3">{{ __('No review submitted yet') }}</div>
        </div>
        @endif
    </div>
    @endif



    <!-- Order Notes -->
    @if($order->notes)
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-notes"></i> {{ __('Order Notes') }}
            </h3>
        </div>
        <div class="card-body">
            <p class="mb-0">{{ $order->notes }}</p>
        </div>
    </div>
    @endif

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Update Order Status') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('New Status') }}</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>{{ __('Processing') }}</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Changing order status will be visible to the customer.') }}
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Update Status') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
