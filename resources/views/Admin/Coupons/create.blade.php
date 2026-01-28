@extends('layouts.admin')

@section('title', 'Create Coupon')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">

        <form method="POST" action="{{ route('admin.coupons.store') }}"
              class="card border-0 shadow-lg">

            @csrf

            {{-- Header --}}
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="avatar bg-white text-primary">
                            <i class="ti ti-ticket"></i>
                        </span>
                    </div>
                    <div>
                        <h3 class="mb-0">Create Coupon</h3>
                        <small class="opacity-75">Define discount rules and usage</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">

                {{-- Coupon Code --}}
                <div class="mb-4">
                    <label class="form-label text-muted">
                        Coupon Code
                    </label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-key"></i>
                        </span>
                        <input
                            type="text"
                            name="code"
                            class="form-control @error('code') is-invalid @enderror"
                            placeholder="SUMMER2024"
                            value="{{ old('code') }}"
                            required
                        >
                    </div>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Discount Section --}}
                <div class="card bg-light border mb-4">
                    <div class="card-body">

                        <h4 class="mb-3">
                            <i class="ti ti-discount-2 text-primary me-1"></i>
                            Discount Details
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Type</label>
                                <select
                                    name="discount_type"
                                    class="form-select @error('discount_type') is-invalid @enderror"
                                    required
                                >
                                    <option value="">Choose type</option>
                                    <option value="percentage" @selected(old('discount_type')==='percentage')>
                                        Percentage (%)
                                    </option>
                                    <option value="fixed" @selected(old('discount_type')==='fixed')>
                                        Fixed Amount
                                    </option>
                                </select>
                                @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discount Amount</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="amount"
                                    class="form-control @error('amount') is-invalid @enderror"
                                    placeholder="Enter value"
                                    value="{{ old('amount') }}"
                                    required
                                >
                                @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Dates --}}
                <div class="card border mb-4">
                    <div class="card-body">

                        <h4 class="mb-3">
                            <i class="ti ti-calendar-event text-success me-1"></i>
                            Validity Period
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Starts At</label>
                                <input
                                    type="datetime-local"
                                    name="starts_at"
                                    class="form-control"
                                    value="{{ old('starts_at') }}"
                                >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expires At</label>
                                <input
                                    type="datetime-local"
                                    name="expires_at"
                                    class="form-control @error('expires_at') is-invalid @enderror"
                                    value="{{ old('expires_at') }}"
                                    required
                                >
                                @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Usage --}}
                <div class="card border mb-4">
                    <div class="card-body">

                        <h4 class="mb-3">
                            <i class="ti ti-users text-warning me-1"></i>
                            Usage Limits
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Usage</label>
                                <input
                                    type="number"
                                    name="max_usage"
                                    class="form-control"
                                    placeholder="Unlimited if empty"
                                    value="{{ old('max_usage') }}"
                                >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Minimum Order Amount</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="min_order_amount"
                                    class="form-control"
                                    placeholder="Optional"
                                    value="{{ old('min_order_amount') }}"
                                >
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Flags --}}
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-check form-switch">
                           <input class="form-check-input" type="checkbox" name="is_active" value="1"
       {{ old('is_active', '1') == '1' ? 'checked' : '' }}>

                            <span class="form-check-label fw-semibold">
                                Active Coupon
                            </span>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="one_time_per_user" value="1">
                            <span class="form-check-label fw-semibold">
                                One time per user
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.coupons.index') }}"
                   class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Back
                </a>

                <button class="btn btn-success px-4">
                    <i class="ti ti-check"></i> Create Coupon
                </button>
            </div>

        </form>

    </div>
</div>

@endsection
