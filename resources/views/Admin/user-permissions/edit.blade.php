@extends('layouts.admin')

@section('title', __('Manage User Permissions') . ' - ' . $user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.show', $user) }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to User') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-lock-access"></i> {{ __('Manage Permissions') }}
            </h2>
            <div class="text-muted mt-1">{{ $user->name }} - {{ $user->email }}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <h3 class="m-0 mb-1">{{ $user->name }}</h3>
                    <div class="text-muted mb-3">{{ $user->email }}</div>
                    <div class="mb-3">
                        <span class="badge bg-{{ $user->role == 'super_admin' ? 'red' : ($user->role == 'admin' ? 'blue' : 'secondary') }} badge-lg">
                            {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Current Permissions') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Role Permissions') }}</div>
                        <div class="fw-bold text-primary">
                            {{ $userRole ? $userRole->permissions->count() : 0 }} {{ __('from role') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Direct Permissions') }}</div>
                        <div class="fw-bold text-success">
                            {{ count($userPermissions) }} {{ __('custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('Role & Permissions') }}</h3>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label required">{{ __('User Role') }}</label>
                        <select name="role" id="user-role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role', $userRole ? $userRole->name : $user->role) == $role->name ? 'selected' : '' }}>
                                    {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
                                    ({{ $role->permissions->count() }} {{ __('permissions') }})
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <h4 class="mb-3">
                            <i class="ti ti-key-plus"></i> {{ __('Additional Direct Permissions') }}
                        </h4>

                        <div class="row">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h4 class="card-title mb-0">
                                                <i class="ti ti-folder text-blue"></i>
                                                {{ __(ucfirst($module)) }}
                                            </h4>

                                            {{-- ✅ التعديل الوحيد هنا --}}
                                            <div class="card-actions">
                                                <label class="form-check form-switch mb-0">
                                                    <input class="form-check-input select-all-module"
                                                           type="checkbox"
                                                           data-module="{{ $module }}">
                                                    <span class="form-check-label small">{{ __('Select All') }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            @foreach($modulePermissions as $permission)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox module-{{ $module }}"
                                                           type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           id="permission_{{ $permission->id }}"
                                                           {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                                           {{ in_array($permission->id, $userRolePermissions) ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        <span class="badge bg-{{ in_array($permission->id, $userRolePermissions) ? 'blue' : 'green' }}-lt">
                                                            {{ $permission->name }}
                                                        </span>
                                                        @if(in_array($permission->id, $userRolePermissions))
                                                            <small class="text-muted">({{ __('from role') }})</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
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
        document.querySelectorAll('.select-all-module').forEach(function(checkbox) {
            const module = checkbox.dataset.module;
            const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');

            updateSelectAllState(checkbox, moduleCheckboxes);

            checkbox.addEventListener('change', function() {
                moduleCheckboxes.forEach(function(cb) {
                    cb.checked = checkbox.checked;
                });
            });
        });

        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const moduleClass = Array.from(this.classList).find(c => c.startsWith('module-'));
                if (!moduleClass) return;

                const module = moduleClass.replace('module-', '');
                const selectAllCheckbox = document.querySelector('[data-module="' + module + '"]');
                const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');

                updateSelectAllState(selectAllCheckbox, moduleCheckboxes);
            });
        });

        function updateSelectAllState(selectAllCheckbox, moduleCheckboxes) {
            if (!selectAllCheckbox) return;

            const checked = Array.from(moduleCheckboxes).filter(cb => cb.checked).length;
            selectAllCheckbox.checked = checked === moduleCheckboxes.length;
            selectAllCheckbox.indeterminate = checked > 0 && checked < moduleCheckboxes.length;
        }
    });
</script>
@endpush













 <!-- #region-->{{-- @extends('layouts.admin')

@section('title', __('Edit User Permissions') . ' - ' . $user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.show', $user) }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to User') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-lock-access"></i> {{ __('Edit Permissions') }}: {{ $user->name }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- User Info Sidebar -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                    <h3 class="m-0 mb-1">{{ $user->name }}</h3>
                    <div class="text-muted mb-3">{{ $user->email }}</div>

                    <div class="mb-3">
                        <span class="badge bg-{{ $user->role == 'super_admin' ? 'red' : ($user->role == 'admin' ? 'blue' : 'secondary') }} badge-lg">
                            {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{ __('Current Permissions') }}</h4>

                    @if($userRole)
                        <div class="mb-3">
                            <div class="text-muted small">{{ __('Role Permissions') }}</div>
                            <div class="fw-bold text-blue">
                                {{ $userRole->permissions->count() }} {{ __('from role') }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Direct Permissions') }}</div>
                        <div class="fw-bold text-green">
                            {{ count($userPermissions) }} {{ __('custom') }}
                        </div>
                    </div>

                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="ti ti-info-circle"></i>
                            {{ __('Direct permissions are added on top of role permissions') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Form -->
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('Role & Permissions') }}</h3>
                </div>

                <div class="card-body">
                    <!-- Select Role -->
                    <div class="mb-4">
                        <label class="form-label required">{{ __('User Role') }}</label>
                        <select name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                                id="roleSelect">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                        {{ old('role', $user->role) == $role->name ? 'selected' : '' }}
                                        data-permissions="{{ $role->permissions->pluck('id')->toJson() }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
                                    ({{ $role->permissions->count() }} {{ __('permissions') }})
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Changing role will inherit all permissions from that role') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <!-- Role Permissions Preview -->
                    <div class="mb-4" id="rolePermissionsPreview">
                        <h4 class="mb-3">
                            <i class="ti ti-shield text-blue"></i>
                            {{ __('Role Permissions') }}
                        </h4>
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            {{ __('These permissions come from the selected role and cannot be removed here') }}
                        </div>
                        <div id="rolePermissionsList" class="d-flex flex-wrap gap-2">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Additional Direct Permissions -->
                    <div class="mb-3">
                        <h4 class="mb-2">
                            <i class="ti ti-key text-green"></i>
                            {{ __('Additional Direct Permissions') }}
                        </h4>
                        <p class="text-muted small mb-3">
                            {{ __('Grant extra permissions beyond the role. These are specific to this user.') }}
                        </p>

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
                                                               data-module="{{ $module }}">
                                                        <span class="form-check-label small">{{ __('Select All') }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input module-{{ $module }} permission-checkbox"
                                                               type="checkbox"
                                                               name="permissions[]"
                                                               value="{{ $permission->id }}"
                                                               id="permission_{{ $permission->id }}"
                                                               data-permission-id="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', $userPermissions)) ? 'checked' : '' }}
                                                               {{ in_array($permission->id, $userRolePermissions) ? 'disabled' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            <span class="badge bg-{{ in_array($permission->id, $userRolePermissions) ? 'blue' : 'green' }}-lt">
                                                                {{ $permission->name }}
                                                            </span>
                                                            @if(in_array($permission->id, $userRolePermissions))
                                                                <small class="text-muted">({{ __('from role') }})</small>
                                                            @endif
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle"></i>
                                {{ __('No permissions available.') }}
                            </div>
                        @endif

                        @error('permissions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer text-end">
                    <div class="d-flex">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-link">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <i class="ti ti-device-floppy"></i> {{ __('Save Permissions') }}
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
        const roleSelect = document.getElementById('roleSelect');
        const rolePermissionsList = document.getElementById('rolePermissionsList');

        // All permissions data (from roles)
        const rolesData = @json($roles->mapWithKeys(function($role) {
            // return [$role->name => $role->permissions->pluck('name', 'id')];
            return [$role->name => $role->permissions->pluck('id')->toArray()];
        }));

        // Update role permissions preview and disable checkboxes
        function updateRolePermissions() {
            const selectedRole = roleSelect.value;
            const rolePermissions = rolesData[selectedRole] ||[];
            // {};
            // const rolePermissionIds = Object.keys(rolePermissions).map(id => parseInt(id));

            // Update preview
            // Update preview
rolePermissionsList.innerHTML = '';

// Get permission names for display
const allPermissionsMap = @json($allPermissions->pluck('name', 'id'));

rolePermissionIds.forEach(id => {
    const badge = document.createElement('span');
    badge.className = 'badge bg-blue-lt';
    badge.textContent = allPermissionsMap[id] || 'Unknown';
    rolePermissionsList.appendChild(badge);
});

            // Enable/disable checkboxes
            document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                const permissionId = parseInt(checkbox.dataset.permissionId);
                const isRolePermission = rolePermissionIds.includes(permissionId);

                checkbox.disabled = isRolePermission;

                // Update label
                const label = checkbox.closest('.form-check').querySelector('label');
                const badge = label.querySelector('.badge');
                const existingNote = label.querySelector('small');

                if (isRolePermission) {
                    badge.className = 'badge bg-blue-lt';
                    if (!existingNote) {
                        const note = document.createElement('small');
                        note.className = 'text-muted';
                        note.textContent = ' (' + '{{ __("from role") }}' + ')';
                        label.appendChild(note);
                    }
                } else {
                    badge.className = 'badge bg-green-lt';
                    if (existingNote) {
                        existingNote.remove();
                    }
                }
            });
        }

        // Initial load
        updateRolePermissions();

        // On role change
        roleSelect.addEventListener('change', updateRolePermissions);

        // Select All functionality for each module
        document.querySelectorAll('.select-all-module').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');

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
                    const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');
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
@endpush --}}






















{{--
@extends('layouts.admin')

@section('title', __('Manage User Permissions') . ' - ' . $user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.show', $user) }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to User') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-lock-access"></i> {{ __('Manage Permissions') }}
            </h2>
            <div class="text-muted mt-1">{{ $user->name }} - {{ $user->email }}</div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-4">
            <!-- User Info Card -->
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <h3 class="m-0 mb-1">{{ $user->name }}</h3>
                    <div class="text-muted mb-3">{{ $user->email }}</div>
                    <div class="mb-3">
                        <span class="badge bg-{{ $user->role == 'super_admin' ? 'red' : ($user->role == 'admin' ? 'blue' : 'secondary') }} badge-lg">
                            {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Current Permissions Summary -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Current Permissions') }}</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Role Permissions') }}</div>
                        <div class="fw-bold text-primary">
                            {{ $userRole ? $userRole->permissions->count() : 0 }} {{ __('from role') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">{{ __('Direct Permissions') }}</div>
                        <div class="fw-bold text-success">
                            {{ count($userPermissions) }} {{ __('custom') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}" class="card">
                @csrf
                @method('PUT')

                <div class="card-header">
                    <h3 class="card-title">{{ __('Role & Permissions') }}</h3>
                </div>

                <div class="card-body">
                    <!-- User Role Selection -->
                    <div class="mb-4">
                        <label class="form-label required">{{ __('User Role') }}</label>
                        <select name="role" id="user-role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role', $userRole ? $userRole->name : $user->role) == $role->name ? 'selected' : '' }}>
                                    {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
                                    ({{ $role->permissions->count() }} {{ __('permissions') }})
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-hint">
                            <i class="ti ti-info-circle"></i>
                            {{ __('Changing role will inherit all permissions from that role') }}
                        </small>
                    </div>

                    <hr class="my-4">

                    <!-- Role Permissions (Read-only) -->
                    <div class="mb-4">
                        <h4 class="mb-3">
                            <i class="ti ti-shield-check"></i> {{ __('Role Permissions') }}
                        </h4>
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            {{ __('These permissions are inherited from the selected role and cannot be modified here.') }}
                        </div>
                        <div id="role-permissions-display" class="d-flex flex-wrap gap-2">
                            @if($userRole)
                                @foreach($userRole->permissions as $permission)
                                    <span class="badge bg-blue-lt">{{ $permission->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">{{ __('No role permissions') }}</span>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Additional Direct Permissions -->
                    <div class="mb-3">
                        <h4 class="mb-3">
                            <i class="ti ti-key-plus"></i> {{ __('Additional Direct Permissions') }}
                        </h4>
                        <p class="text-muted">
                            {{ __('Grant extra permissions beyond the role. These are specific to this user.') }}
                        </p>

                        <div class="row">
                            @foreach($permissions as $module => $modulePermissions)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h4 class="card-title mb-0">
                                                    <i class="ti ti-folder text-blue"></i>
                                                    {{ __(ucfirst($module)) }}
                                                </h4>
                                                <label class="form-check form-switch mb-0">
                                                    <input class="form-check-input select-all-module"
                                                           type="checkbox"
                                                           data-module="{{ $module }}">
                                                    <span class="form-check-label small">{{ __('Select All') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @foreach($modulePermissions as $permission)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox module-{{ $module }}"
                                                           type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           id="permission_{{ $permission->id }}"
                                                           {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                                           {{ in_array($permission->id, $userRolePermissions) ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        <span class="badge bg-{{ in_array($permission->id, $userRolePermissions) ? 'blue' : 'green' }}-lt">
                                                            {{ $permission->name }}
                                                        </span>
                                                        @if(in_array($permission->id, $userRolePermissions))
                                                            <small class="text-muted">({{ __('from role') }})</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('permissions')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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
        // Select All functionality for each module
        document.querySelectorAll('.select-all-module').forEach(function(checkbox) {
            const module = checkbox.dataset.module;
            const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');

            // Set initial state
            updateSelectAllState(checkbox, moduleCheckboxes);

            // Toggle all checkboxes in module
            checkbox.addEventListener('change', function() {
                moduleCheckboxes.forEach(function(cb) {
                    cb.checked = checkbox.checked;
                });
            });
        });

        // Update "Select All" checkbox when individual checkboxes change
        document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const classList = Array.from(this.classList);
                const moduleClass = classList.find(c => c.startsWith('module-'));

                if (moduleClass) {
                    const module = moduleClass.replace('module-', '');
                    const selectAllCheckbox = document.querySelector('[data-module="' + module + '"]');
                    const moduleCheckboxes = document.querySelectorAll('.module-' + module + ':not(:disabled)');

                    updateSelectAllState(selectAllCheckbox, moduleCheckboxes);
                }
            });
        });

        function updateSelectAllState(selectAllCheckbox, moduleCheckboxes) {
            if (selectAllCheckbox && moduleCheckboxes.length > 0) {
                const checkedCount = Array.from(moduleCheckboxes).filter(cb => cb.checked).length;
                selectAllCheckbox.checked = checkedCount === moduleCheckboxes.length;
                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < moduleCheckboxes.length;
            }
        }
    });
</script>
@endpush --}}
