@extends('layouts.admin')

@section('title', __('Edit User') . ' - ' . $user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.show', $user) }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to User') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-edit"></i> {{ __('Edit User') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('User Information') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Full Name') }}</label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="{{ __('Enter full name') }}"
                               value="{{ old('name', $user->name) }}"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Email Address') }}</label>
                        <input type="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="email@example.com"
                               value="{{ old('email', $user->email) }}"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Phone Number') }}</label>
                        <input type="tel"
                               name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               placeholder="+963 XXX XXX XXX"
                               value="{{ old('phone', $user->phone) }}"
                               required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Role') }}</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                    {{ __(ucfirst(str_replace('_', ' ', $role))) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            {{ __('Changing role will update user permissions.') }}
                        </small>
                    </div>

                    <hr>

                    <h4 class="mb-3">{{ __('Change Password') }}</h4>
                    <p class="text-muted">{{ __('Leave blank to keep current password') }}</p>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Password') }}</label>
                        <input type="password"
                               name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('Enter new password') }}"
                               autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Confirm Password') }}</label>
                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="{{ __('Confirm new password') }}"
                               autocomplete="new-password">
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
