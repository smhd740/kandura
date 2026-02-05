@extends('layouts.admin')

@section('title', __('Transactions'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-receipt"></i> {{ __('Transactions') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View all payment transactions') }}</div>
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
                        <div class="subheader">{{ __('Total Transactions') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-primary">{{ $stats['total_transactions'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['total_transactions'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Volume') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-success">${{ number_format($stats['total_volume'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['total_volume'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Admin Credits') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-info">${{ number_format($stats['admin_credits'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['admin_credits'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Order Payments') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-orange">${{ number_format($stats['order_payments'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['order_payments'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.transactions.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('Search User') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('Name or email...') }}"
                           value="{{ request('search') }}">
                </div>

                {{-- <div class="col-md-2">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>{{ __('Order Payment') }}</option>
                        <option value="admin_credit" {{ request('type') == 'admin_credit' ? 'selected' : '' }}>{{ __('Admin Credit') }}</option>
                    </select>
                </div> --}}

                {{-- <div class="col-md-2">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    </select>
                </div> --}}


                <div class="col-md-2">
    <label class="form-label">{{ __('Type') }}</label>
    <select name="type" class="form-select">
        <option value="">{{ __('All Types') }}</option>
        <option value="order" {{ request('type') == 'order' ? 'selected' : '' }}>{{ __('Order') }}</option>
        <option value="wallet" {{ request('type') == 'wallet' ? 'selected' : '' }}>{{ __('Wallet') }}</option>
        <option value="admin_credit" {{ request('type') == 'admin_credit' ? 'selected' : '' }}>{{ __('Admin Credit') }}</option>
        <option value="admin_debit" {{ request('type') == 'admin_debit' ? 'selected' : '' }}>{{ __('Admin Debit') }}</option>
    </select>
</div>

<div class="col-md-2">
    <label class="form-label">{{ __('Status') }}</label>
    <select name="status" class="form-select">
        <option value="">{{ __('All Status') }}</option>
        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>{{ __('Complete') }}</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
        <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
    </select>
</div>


                <div class="col-md-2">
                    <label class="form-label">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>

                {{-- <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter"></i>
                    </button>
                </div> --}}

                <div class="col-md-1">
    <label class="form-label">&nbsp;</label>
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-filter"></i> {{ __('Filter') }}
        </button>
        @if(request()->hasAny(['search', 'type', 'status', 'date_from', 'date_to']))
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">
                <i class="ti ti-x"></i> {{ __('Clear') }}
            </a>
        @endif
    </div>
</div>

            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Transactions') }} ({{ $transactions->total() }})</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Description') }}</th>
                        <th>{{ __('Date') }}</th>
                        {{-- <th class="w-1">{{ __('Actions') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td><span class="text-muted">#{{ $transaction->id }}</span></td>

                           {{-- <td>
    @if($transaction->wallet && $transaction->wallet->user)
        <div>{{ $transaction->wallet->user->name }}</div>
        <div class="text-muted small">{{ $transaction->wallet->user->email }}</div>
    @else
        <div class="text-muted">{{ __('N/A') }}</div>
    @endif
</td> --}}

<td>
    @php
        $user = $transaction->user ?? $transaction->wallet?->user ?? $transaction->order?->user;
    @endphp

    @if($user)
        <div>{{ $user->name }}</div>
        <div class="text-muted small">{{ $user->email }}</div>
    @else
        <div class="text-muted">{{ __('System') }}</div>
    @endif
</td>

                            <td>
                                <span class="badge bg-{{
                                    $transaction->type == 'order' ? 'danger' :
                                    ($transaction->type == 'admin_credit' ? 'success' : 'info')
                                }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $transaction->type))) }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold {{ $transaction->type == 'order' ? 'text-danger' : 'text-success' }}">
                                    {{ $transaction->type == 'order' ? '-' : '+' }}${{ number_format($transaction->amount, 2) }}
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-{{ $transaction->status == 'complete' ? 'success' : ($transaction->status == 'paid' ? 'info' : ($transaction->status == 'cancel' ? 'danger' : 'warning')) }}">
    {{ __(ucfirst($transaction->status)) }}
</span>
                            </td>

                            <td>
                                <div class="text-truncate" style="max-width: 200px;">
                                    {{ $transaction->description }}
                                </div>
                                @if($transaction->order)
                                    <a href="{{ route('admin.orders.show', $transaction->order) }}" class="small">
                                        #{{ $transaction->order->order_number }}
                                    </a>
                                @endif
                            </td>

                            <td>
                                <div>{{ $transaction->created_at->format('Y-m-d') }}</div>
                                <div class="text-muted small">{{ $transaction->created_at->format('H:i') }}</div>
                            </td>

                            {{-- <td>
                                <a href="{{ route('admin.transactions.show', $transaction) }}"
                                   class="btn btn-sm btn-primary"
                                   title="{{ __('View Details') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-receipt"></i>
                                    </div>
                                    <p class="empty-title">{{ __('No transactions found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                {{ __('Showing') }} {{ $transactions->firstItem() }} {{ __('to') }} {{ $transactions->lastItem() }}
                {{ __('of') }} {{ $transactions->total() }} {{ __('entries') }}
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $transactions->links() }}
            </ul>
        </div>
        @endif
    </div>
@endsection
