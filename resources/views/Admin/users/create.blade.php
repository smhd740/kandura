@extends('layouts.admin')

@section('title', __('Create New User'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Users') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-user-plus"></i> {{ __('Create New User') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form method="POST" action="{{ route('admin.users.store') }}" class="card">
                @csrf

                <div class="card-header">
                    <h3 class="card-title">{{ __('User Information') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Full Name') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-user"></i>
                            </span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="{{ __('Enter full name') }}"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Email Address') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-mail"></i>
                            </span>
                            <input type="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@example.com"
                                   value="{{ old('email') }}"
                                   required>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Phone Number') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-phone"></i>
                            </span>
                            <input type="tel"
                                   name="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="+963 XXX XXX XXX"
                                   value="{{ old('phone') }}"
                                   required>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            {{ __('Enter with country code (e.g., +963 XXX XXX XXX)') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">{{ __('Account Settings') }}</h4>

                   <!-- Role -->
<div class="mb-3">
    <label class="form-label required">{{ __('Role') }}</label>
    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
        <option value="">{{ __('Select role...') }}</option>

        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
            @if($role->name !== 'super_admin' || auth()->user()->role === 'super_admin')
                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                    {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
                </option>
            @endif
        @endforeach

    </select>

    @error('role')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror

    <small class="form-hint">
        <i class="ti ti-info-circle"></i>
        @foreach(\Spatie\Permission\Models\Role::orderBy('name')->get() as $role)
            @if($role->name !== 'super_admin' || auth()->user()->role === 'super_admin')
                <strong>{{ __(ucfirst(str_replace('_', ' ', $role->name))) }}:</strong>
                {{ __('Role permissions depend on system configuration') }}<br>
            @endif
        @endforeach
    </small>
</div>


                    <!-- Active Status -->
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('Active Account') }}</span>
                        </label>
                        <small class="form-hint">
                            {{ __('Inactive accounts cannot login to the system') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <h4 class="mb-3">{{ __('Password') }}</h4>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Password') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-lock"></i>
                            </span>
                            <input type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="{{ __('Minimum 8 characters') }}"
                                   required
                                   autocomplete="new-password">
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Confirm Password') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-lock"></i>
                            </span>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   placeholder="{{ __('Confirm password') }}"
                                   required
                                   autocomplete="new-password">
                        </div>
                    </div>

                    <!-- Email Verification -->
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="email_verified" value="1" {{ old('email_verified', true) ? 'checked' : '' }}>
                            <span class="form-check-label">{{ __('Mark email as verified') }}</span>
                        </label>
                        <small class="form-hint">
                            {{ __('If unchecked, user will need to verify their email address') }}
                        </small>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-user-plus"></i> {{ __('Create User') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
    document.addEventListener('DOMContentLoaded', function() {
        // You can add password strength checker here if needed
    });
</script>
@endpush
