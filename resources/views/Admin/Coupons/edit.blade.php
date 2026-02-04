@extends('layouts.admin')

@section('title', __('Edit Coupon') . ' - ' . $coupon->code)

@section('header')
<div class="row g-2 align-items-center">
    <div class="col">
        <div class="page-pretitle">
            <a href="{{ route('admin.coupons.index') }}" class="text-muted">
                <i class="ti ti-arrow-left"></i> {{ __('Back to Coupons') }}
            </a>
        </div>
        <h2 class="page-title">
            <i class="ti ti-edit"></i> {{ __('Edit Coupon') }} - {{ $coupon->code }}
        </h2>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}" class="card">
            @csrf
            @method('PUT')

            <div class="card-header">
                <h3 class="card-title">{{ __('Coupon Information') }}</h3>
            </div>

            <div class="card-body">
                <!-- Coupon Code -->
                <div class="mb-3">
                    <label class="form-label required">{{ __('Coupon Code') }}</label>
                    <input type="text"
                           name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $coupon->code) }}"
                           required
                           style="text-transform: uppercase;">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Discount Type & Amount -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Discount Type') }}</label>
                            <select name="discount_type"
                                    class="form-select @error('discount_type') is-invalid @enderror"
                                    required>
                                <option value="percentage" @selected(old('discount_type', $coupon->discount_type) == 'percentage')>
                                    {{ __('Percentage') }}
                                </option>
                                <option value="fixed" @selected(old('discount_type', $coupon->discount_type) == 'fixed')>
                                    {{ __('Fixed Amount') }}
                                </option>
                            </select>
                            @error('discount_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Discount Value') }}</label>
                            <input type="number"
                                   name="amount"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('amount', $coupon->amount) }}"
                                   required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Valid Period -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Valid From') }}</label>
                            <input type="datetime-local"
                                   name="starts_at"
                                   class="form-control @error('starts_at') is-invalid @enderror"
                                   value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}">
                            @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">{{ __('Valid To') }}</label>
                            <input type="datetime-local"
                                   name="expires_at"
                                   class="form-control @error('expires_at') is-invalid @enderror"
                                   value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}"
                                   required>
                            @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <!-- Max Usage -->
                <div class="mb-3">
                    <label class="form-label required">{{ __('Maximum Uses') }}</label>
                    <input type="number"
                           name="max_usage"
                           class="form-control @error('max_usage') is-invalid @enderror"
                           min="1"
                           value="{{ old('max_usage', $coupon->max_usage) }}"
                           required>
                    <small class="form-hint">
                        {{ __('Current uses') }}: {{ $coupon->used_count }}
                    </small>
                    @error('max_usage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>


                <!-- Active Status -->
<div class="mb-3 form-check form-switch">
    <input type="checkbox"
           name="is_active"
           value="1"
           class="form-check-input"
           id="is_active"
           @checked(old('is_active', $coupon->is_active))>
    <label class="form-check-label" for="is_active">
        {{ __('Active Coupon') }}
    </label>
</div>

<!-- One Time Per User -->
<div class="mb-3 form-check">
    <input type="checkbox"
           name="one_time_per_user"
           value="1"
           class="form-check-input"
           id="one_time_per_user"
           @checked(old('one_time_per_user', $coupon->one_time_per_user))>
    <label class="form-check-label" for="one_time_per_user">
        {{ __('One time per user') }}
    </label>
</div>



                <!-- User Specific Checkbox -->
                <div class="mb-3 form-check">
                    <input type="checkbox"
                           name="is_user_specific"
                           value="1"
                           class="form-check-input"
                           id="is_user_specific"
                           @checked(old('is_user_specific', $coupon->is_user_specific))>
                    <label class="form-check-label" for="is_user_specific">
                        {{ __('Restrict to selected users only') }}
                    </label>
                </div>

                <!-- Allowed Users -->
                <div class="mb-3" id="allowed-users-wrapper">
                    <label class="form-label">{{ __('Allowed Users') }}</label>
                    <select name="allowed_users[]" class="form-select" multiple size="8">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @selected(in_array(
                                    $user->id,
                                    old('allowed_users', $coupon->allowedUsers->pluck('id')->toArray())
                                ))>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('allowed_users')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>

            <div class="card-footer text-end">
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
                <button class="btn btn-primary ms-auto">
                    <i class="ti ti-device-floppy"></i> {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const checkbox = document.getElementById('is_user_specific');
    const wrapper = document.getElementById('allowed-users-wrapper');

    function toggleAllowedUsers() {
        wrapper.style.display = checkbox.checked ? '' : 'none';
    }

    // نفّذ عند التحميل
    toggleAllowedUsers();

    // أضف مستمع للتغيير
    checkbox.addEventListener('change', toggleAllowedUsers);
</script>
@endsection
