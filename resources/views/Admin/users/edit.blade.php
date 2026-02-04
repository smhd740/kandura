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

                    <hr class="my-4">

                    <h4 class="mb-3">{{ __('Role Settings') }}</h4>

                    <!-- Base Role -->
                    <div class="mb-3">
                        <label class="form-label required">{{ __('Base Role') }}</label>
                        <select name="role" id="base-role" class="form-select @error('role') is-invalid @enderror" required
                            {{ auth()->user()->role !== 'super_admin' ? 'disabled' : '' }}>

                            @if(auth()->user()->role === 'super_admin')
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ __(ucfirst(str_replace('_', ' ', $role))) }}
                                    </option>
                                @endforeach
                            @else
                                <!-- Admin can only see current role -->
                                <option value="{{ $user->role }}" selected>
                                    {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
                                </option>
                            @endif
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if(auth()->user()->role !== 'super_admin')
                            <input type="hidden" name="role" value="{{ $user->role }}">
                            <small class="form-hint text-danger">
                                <i class="ti ti-lock"></i>
                                {{ __('Only Super Admin can change user roles') }}
                            </small>
                        @endif
                    </div>

                    <!-- Admin Specific Role (للسوبر أدمن فقط) -->
                    @if(auth()->user()->role === 'super_admin')
                    <div class="mb-3" id="admin-role-section" style="display: {{ old('role', $user->role) == 'admin' ? 'block' : 'none' }};">
                        <label class="form-label">{{ __('Admin Specific Role') }}</label>
                        <select name="admin_role" class="form-select @error('admin_role') is-invalid @enderror">
                            <option value="">{{ __('General Admin (No specific role)') }}</option>
                            @foreach(\Spatie\Permission\Models\Role::whereNotIn('name', ['super_admin', 'admin', 'user', 'guest'])->orderBy('name')->get() as $spatieRole)
                                <option value="{{ $spatieRole->name }}"
                                    {{ old('admin_role', $user->roles->first()?->name) == $spatieRole->name ? 'selected' : '' }}>
                                    {{ __(ucfirst(str_replace('_', ' ', $spatieRole->name))) }}
                                    @if($spatieRole->permissions->count() > 0)
                                        ({{ $spatieRole->permissions->count() }} {{ __('permissions') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('admin_role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Current role:') }}
                            <strong>{{ $user->roles->first()?->name ? __(ucfirst(str_replace('_', ' ', $user->roles->first()->name))) : __('None') }}</strong>
                        </small>
                    </div>
                    @endif

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const baseRoleSelect = document.getElementById('base-role');
        const adminRoleSection = document.getElementById('admin-role-section');

        function toggleAdminRoleSection() {
            if (baseRoleSelect && adminRoleSection) {
                if (baseRoleSelect.value === 'admin') {
                    adminRoleSection.style.display = 'block';
                } else {
                    adminRoleSection.style.display = 'none';
                }
            }
        }

        toggleAdminRoleSection();

        if (baseRoleSelect) {
            baseRoleSelect.addEventListener('change', toggleAdminRoleSection);
        }
    });
</script>
@endpush
