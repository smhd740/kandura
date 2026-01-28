@extends('layouts.admin')

@section('title', __('Role Details') . ' - ' . $role->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.roles.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Roles') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-shield-lock"></i> {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @if(!in_array($role->name, ['super_admin', 'admin', 'user', 'guest']))
                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                    <i class="ti ti-edit"></i> {{ __('Edit Role') }}
                </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-3">
        <!-- Role Info Card -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-xl bg-{{
                                $role->name == 'super_admin' ? 'red' :
                                ($role->name == 'admin' ? 'blue' :
                                ($role->name == 'user' ? 'green' : 'secondary'))
                            }}-lt">
                                <i class="ti ti-shield-lock fs-1"></i>
                            </span>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">{{ __(ucfirst(str_replace('_', ' ', $role->name))) }}</h2>
                            @if(in_array($role->name, ['super_admin', 'admin', 'user', 'guest']))
                                <span class="badge bg-yellow">{{ __('System Role') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Custom Role') }}</span>
                            @endif
                        </div>
                        <div class="col-auto">
                            <div class="row g-3 text-center">
                                <div class="col">
                                    <div class="text-muted small">{{ __('Permissions') }}</div>
                                    <div class="h2 mb-0">{{ $role->permissions->count() }}</div>
                                </div>
                                <div class="col">
                                    <div class="text-muted small">{{ __('Users') }}</div>
                                    <div class="h2 mb-0">{{ $role->users->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-key"></i> {{ __('Role Permissions') }}
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-blue">{{ $role->permissions->count() }}</span>
                    </div>
                </div>

                @if($role->permissions->count() > 0)
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($role->permissions as $permission)
                                <div class="col-md-6">
                                    <div class="btn btn-outline-primary w-100 text-start" disabled>
                                        <i class="ti ti-key me-2"></i>
                                        {{ $permission->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-key"></i>
                            </div>
                            <p class="empty-title">{{ __('No permissions assigned') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('This role has no permissions.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Users Section -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users"></i> {{ __('Users with this Role') }}
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-green">{{ $role->users->count() }}</span>
                    </div>
                </div>

                @if($role->users->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($role->users as $user)
                            <a href="{{ route('admin.users.show', $user) }}" class="list-group-item list-group-item-action">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar avatar-sm">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ __('ID') }}: {{ $user->id }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-users"></i>
                            </div>
                            <p class="empty-title">{{ __('No users found') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('No users have been assigned this role yet.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Role Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle"></i> {{ __('Role Information') }}
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">{{ __('Role Name') }}</div>
                            <div class="col-auto">
                                <span class="badge bg-blue">{{ $role->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">{{ __('Guard') }}</div>
                            <div class="col-auto text-muted">{{ $role->guard_name }}</div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">{{ __('Created') }}</div>
                            <div class="col-auto text-muted">{{ $role->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col">{{ __('Last Updated') }}</div>
                            <div class="col-auto text-muted">{{ $role->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
