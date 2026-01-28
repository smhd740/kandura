@extends('layouts.admin')

@section('title', __('Wallets Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-wallet"></i> {{ __('Wallets Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Manage user wallets and balances') }}</div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Wallets') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-primary">{{ $stats['total_wallets'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['total_wallets'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Balance') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-success">${{ number_format($stats['total_balance'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['total_balance'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Average Balance') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-info">${{ number_format($stats['avg_balance'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['avg_balance'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.wallets.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search User') }}</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="{{ __('Name or email...') }}"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Min Balance') }}</label>
                    <input type="number" name="balance_min" class="form-control"
                           step="0.01" placeholder="0.00"
                           value="{{ request('balance_min') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Max Balance') }}</label>
                    <input type="number" name="balance_max" class="form-control"
                           step="0.01" placeholder="0.00"
                           value="{{ request('balance_max') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'balance_min', 'balance_max']))
                            <a href="{{ route('admin.wallets.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Wallets Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('All Wallets') }} ({{ $wallets->total() }})</h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>{{ __('Transactions') }}</th>
                        <th>{{ __('Last Updated') }}</th>
                        <th class="w-1">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wallets as $wallet)
                        <tr>
                            <!-- User -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar me-2">{{ strtoupper(substr($wallet->user->name, 0, 2)) }}</span>
                                    <div>
                                        <div class="fw-bold">
                                            <a href="{{ route('admin.users.show', $wallet->user) }}">
                                                {{ $wallet->user->name }}
                                            </a>
                                        </div>
                                        <div class="text-muted small">{{ $wallet->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Balance -->
                            <td>
                                <div class="h3 mb-0 {{ $wallet->amount > 0 ? 'text-success' : 'text-muted' }}">
                                    ${{ number_format($wallet->amount, 2) }}
                                </div>
                            </td>

                            <!-- Transactions Count -->
                            <td>
                                <span class="badge bg-info">
                                    {{ $wallet->transactions()->count() }} {{ __('transactions') }}
                                </span>
                            </td>

                            <!-- Last Updated -->
                            <td>
                                <div>{{ $wallet->updated_at->format('Y-m-d') }}</div>
                                <div class="text-muted small">{{ $wallet->updated_at->diffForHumans() }}</div>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.wallets.show', $wallet) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ __('View Details') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    {{-- <button type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addBalanceModal{{ $wallet->id }}"
                                            title="{{ __('Add Balance') }}">
                                        <i class="ti ti-plus"></i>
                                    </button> --}}
                                </div>
                            </td>
                        </tr>

                        <!-- Add Balance Modal -->
                        {{-- <div class="modal fade" id="addBalanceModal{{ $wallet->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.wallets.add-balance', $wallet) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Add Balance') }} - {{ $wallet->user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Current Balance') }}</label>
                                                <div class="h3">${{ number_format($wallet->amount, 2) }}</div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label required">{{ __('Amount to Add') }}</label>
                                                <input type="number"
                                                       name="amount"
                                                       class="form-control"
                                                       placeholder="0.00"
                                                       step="0.01"
                                                       min="0.01"
                                                       required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Description') }}</label>
                                                <textarea name="description"
                                                          class="form-control"
                                                          rows="3"
                                                          placeholder="{{ __('Optional note...') }}"></textarea>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                {{ __('Cancel') }}
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="ti ti-plus"></i> {{ __('Add Balance') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>--}}
                    @empty

                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-wallet"></i>
                                    </div>
                                    <p class="empty-title">{{ __('No wallets found') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wallets->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                {{ __('Showing') }} {{ $wallets->firstItem() }} {{ __('to') }} {{ $wallets->lastItem() }}
                {{ __('of') }} {{ $wallets->total() }} {{ __('entries') }}
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $wallets->links() }}
            </ul>
        </div>
        @endif
    </div>
@endsection
