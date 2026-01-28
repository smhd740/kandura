@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')

<div class="row mb-3">
    <div class="col">
        <h2>Coupons</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            Create Coupon
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3">

            <div class="col-md-4">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by code"
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="percentage" @selected(request('type')==='percentage')>percentage</option>
                    <option value="fixed" @selected(request('type')==='fixed')>fixed</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="expired" @selected(request('status')==='expired')>Expired</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                    <option value="used_up" @selected(request('status')==='used_up')>Used Up</option>
                </select>
            </div>

            <div class="col-md-2 d-grid gap-2">
                <button class="btn btn-primary">Search</button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                    Clear
                </a>
            </div>

        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Period</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>

            @forelse($coupons as $coupon)
                <tr>
                    <td>{{ $coupon->code }}</td>

                    <td>{{ ucfirst($coupon->discount_type) }}</td>

                    <td>
                        @if($coupon->discount_type === 'percentage')
                            {{ $coupon->amount }} %
                        @else
                            {{ $coupon->amount }}
                        @endif
                    </td>

                    <td>
                        {{ $coupon->starts_at?->format('Y-m-d') ?? '-' }}
                        →
                        {{ $coupon->expires_at?->format('Y-m-d') }}
                    </td>

                    <td>
                        {{ $coupon->used_count }}
                        /
                        {{ $coupon->max_usage ?? '∞' }}
                    </td>

                    <td>
                        @if($coupon->isActive())
                            <span class="badge bg-success">Active</span>
                        @elseif($coupon->isExpired())
                            <span class="badge bg-danger">Expired</span>
                        @elseif($coupon->isFullyUsed())
                            <span class="badge bg-warning">Used Up</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>

                    {{-- Actions --}}
<td>
    <div class="btn-group" role="group">
        <a href="{{ route('admin.coupons.edit', $coupon) }}"
           class="btn btn-sm btn-primary"
           title="Edit">
            <i class="ti ti-edit"></i>
        </a>

        <form method="POST"
              action="{{ route('admin.coupons.toggle-status', $coupon) }}"
              class="d-inline">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="btn btn-sm btn-{{ $coupon->is_active ? 'warning' : 'success' }}"
                    title="{{ $coupon->is_active ? 'Deactivate' : 'Activate' }}">
                <i class="ti ti-{{ $coupon->is_active ? 'x' : 'check' }}"></i>
            </button>
        </form>

        <form method="POST"
              action="{{ route('admin.coupons.destroy', $coupon) }}"
              class="d-inline"
              onsubmit="return confirm('Delete this coupon?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-sm btn-danger"
                    title="Delete">
                <i class="ti ti-trash"></i>
            </button>
        </form>
    </div>
</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        No coupons found
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $coupons->links() }}
    </div>
</div>

@endsection
