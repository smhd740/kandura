@extends('layouts.admin')

@section('title', __('Edit Role') . ' - ' . $role->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.roles.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Roles') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-edit"></i> {{ __('Edit Role') }}: {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('Role Information') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Role Name -->
                    <div class="mb-4">
                        <label class="form-label required">{{ __('Role Name') }}</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <i class="ti ti-shield"></i>
                            </span>
                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="{{ __('e.g., content_manager, moderator') }}"
                                   value="{{ old('name', $role->name) }}"
                                   required>
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Use lowercase with underscores (e.g., content_manager)') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <!-- Role Statistics -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="text-muted small">{{ __('Users with this role') }}</div>
                                        <div class="h3 mb-0">
                                            <i class="ti ti-users text-blue"></i>
                                            {{ $role->users()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <div class="text-muted small">{{ __('Total Permissions') }}</div>
                                        <div class="h3 mb-0">
                                            <i class="ti ti-key text-green"></i>
                                            {{ $role->permissions()->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Permissions Selection -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('Assign Permissions') }}</label>
                        <p class="text-muted small mb-3">{{ __('Select the permissions for this role') }}</p>

                        @if($permissions->count() > 0)
                            <div class="row">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="col-md-6 mb-4">
                                        <div class="card border">
                                            <div class="card-header bg-light">
                                                <h4 class="card-title mb-0">
                                                    <i class="ti ti-folder text-blue"></i>
                                                    {{ __(ucfirst($module)) }}
                                                </h4>
                                                <div class="card-actions">
                                                    <label class="form-check form-switch mb-0">
                                                        <input class="form-check-input select-all-module"
                                                               type="checkbox"
                                                               data-module="{{ $module }}"
                                                               {{ collect($modulePermissions)->pluck('id')->diff($rolePermissions)->isEmpty() ? 'checked' : '' }}>
                                                        <span class="form-check-label small">{{ __('Select All') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input module-{{ $module }}"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               id="permission_{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            <span class="badge bg-blue-lt">{{ $permission->name }}</span>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle"></i>
                                {{ __('No permissions available. Please create permissions first.') }}
                                <a href="{{ route('admin.permissions.create') }}" class="alert-link">
                                    {{ __('Create Permission') }}
                                </a>
                            </div>
                        @endif

                        @error('permissions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-link">
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
        // Select All functionality for each module
        document.querySelectorAll('.select-all-module').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const moduleCheckboxes = document.querySelectorAll('.module-' + module);

                moduleCheckboxes.forEach(function(cb) {
                    cb.checked = checkbox.checked;
                });
            });
        });

        // Update "Select All" checkbox when individual checkboxes change
        document.querySelectorAll('[class*="module-"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const classList = Array.from(this.classList);
                const moduleClass = classList.find(c => c.startsWith('module-'));

                if (moduleClass) {
                    const module = moduleClass.replace('module-', '');
                    const moduleCheckboxes = document.querySelectorAll('.module-' + module);
                    const selectAllCheckbox = document.querySelector('[data-module="' + module + '"]');

                    const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = someChecked && !allChecked;
                    }
                }
            });
        });
    });
</script>
@endpush
