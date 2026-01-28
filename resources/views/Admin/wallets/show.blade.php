@extends('layouts.admin')

@section('title', __('Wallet Details') . ' - ' . $wallet->user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.wallets.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Wallets') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-wallet"></i> {{ __('Wallet') }} - {{ $wallet->user->name }}
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <button type="button"
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#addBalanceModal">
                    <i class="ti ti-plus"></i> {{ __('Add Balance') }}
                </button>
                <button type="button"
                        class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deductBalanceModal">
                    <i class="ti ti-minus"></i> {{ __('Deduct Balance') }}
                </button>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <!-- Wallet Info Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Wallet Information') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <span class="avatar avatar-xl">{{ strtoupper(substr($wallet->user->name, 0, 2)) }}</span>
                        <h3 class="mt-2 mb-1">{{ $wallet->user->name }}</h3>
                        <div class="text-muted">{{ $wallet->user->email }}</div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Current Balance') }}</label>
                        <div class="h1 mb-0 text-success">${{ number_format($wallet->amount, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Total Transactions') }}</label>
                        <div class="h3 mb-0">{{ $wallet->transactions()->count() }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Last Updated') }}</label>
                        <div>{{ $wallet->updated_at->format('Y-m-d H:i') }}</div>
                        <div class="text-muted small">{{ $wallet->updated_at->diffForHumans() }}</div>
                    </div>
                </div>

                {{-- <div class="card-footer">
                    <div class="row g-2">
                        <div class="col">
                            <button type="button"
                                    class="btn btn-success w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#addBalanceModal">
                                <i class="ti ti-plus"></i> {{ __('Add Balance') }}
                            </button>
                        </div>
                        <div class="col">
                            <button type="button"
                                    class="btn btn-danger w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deductBalanceModal">
                                <i class="ti ti-minus"></i> {{ __('Deduct Balance') }}
                            </button>
                        </div>
                    </div>
                </div> --}}

            </div>
        </div>

        <!-- Transactions List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-receipt"></i> {{ __('Transaction History') }}
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($wallet->transactions()->latest()->limit(50)->get() as $transaction)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar bg-{{
                                        $transaction->type == 'order' ? 'danger' :
                                        ($transaction->type == 'admin_credit' ? 'success' : 'info')
                                    }}-lt">
                                        <i class="ti ti-{{
                                            $transaction->type == 'order' ? 'shopping-cart' :
                                            ($transaction->type == 'admin_credit' ? 'plus' : 'arrow-down')
                                        }}"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="fw-bold">
                                        {{ __(ucfirst(str_replace('_', ' ', $transaction->type))) }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $transaction->description }}
                                        @if($transaction->order)
                                            <a href="{{ route('admin.orders.show', $transaction->order) }}">
                                                #{{ $transaction->order->order_number }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="text-muted small">
                                        {{ $transaction->created_at->format('Y-m-d H:i') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="h3 mb-0 {{ $transaction->type == 'order' ? 'text-danger' : 'text-success' }}">
                                        {{ $transaction->type == 'order' ? '-' : '+' }}${{ number_format($transaction->amount, 2) }}
                                    </div>
                                    <div class="text-center">
                                        <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ __(ucfirst($transaction->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item text-center py-5">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-receipt"></i>
                                </div>
                                <p class="empty-title">{{ __('No transactions yet') }}</p>
                                <p class="empty-subtitle text-muted">
                                    {{ __('Transaction history will appear here.') }}
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Balance Modal -->
    <div class="modal fade" id="addBalanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.wallets.add-balance', $wallet) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Add Balance') }}</h5>
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
    </div>

    <!-- Deduct Balance Modal -->
    <div class="modal fade" id="deductBalanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.wallets.deduct-balance', $wallet) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Deduct Balance') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Current Balance') }}</label>
                            <div class="h3">${{ number_format($wallet->amount, 2) }}</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">{{ __('Amount to Deduct') }}</label>
                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $wallet->amount }}"
                                   required>
                            <small class="form-hint text-danger">
                                {{ __('Maximum') }}: ${{ number_format($wallet->amount, 2) }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Reason') }}</label>
                            <textarea name="description"
                                      class="form-control"
                                      rows="3"
                                      placeholder="{{ __('Reason for deduction...') }}"
                                      required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-minus"></i> {{ __('Deduct Balance') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
