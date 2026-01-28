@extends('layouts.admin')

@section('title', __('Roles Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-shield-lock"></i> {{ __('Roles Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Manage user roles and their permissions') }}</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> {{ __('Create Role') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Roles Grid -->
    <div class="row row-cards">
        @foreach($roles as $role)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top bg-{{
                        $role->name == 'super_admin' ? 'red' :
                        ($role->name == 'admin' ? 'blue' :
                        ($role->name == 'user' ? 'green' : 'secondary'))
                    }}"></div>

                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col-auto">
                                <span class="avatar bg-{{
                                    $role->name == 'super_admin' ? 'red' :
                                    ($role->name == 'admin' ? 'blue' :
                                    ($role->name == 'user' ? 'green' : 'secondary'))
                                }}-lt">
                                    <i class="ti ti-shield-lock fs-1"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="fw-bold fs-3">
                                    {{ __(ucfirst(str_replace('_', ' ', $role->name))) }}
                                </div>
                                <div class="text-muted">
                                    @if(in_array($role->name, ['super_admin', 'admin', 'user', 'guest']))
                                        <span class="badge bg-yellow">{{ __('System Role') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Custom Role') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Permissions') }}</div>
                                    <div class="fw-bold">
                                        <i class="ti ti-key text-blue"></i>
                                        {{ $role->permissions_count }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Users') }}</div>
                                    <div class="fw-bold">
                                        <i class="ti ti-users text-green"></i>
                                        {{ $role->users_count }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($role->permissions_count > 0)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <strong>{{ __('Sample Permissions') }}:</strong><br>
                                    @foreach($role->permissions->take(3) as $permission)
                                        <span class="badge bg-blue-lt me-1 mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions_count > 3)
                                        <span class="text-muted">+{{ $role->permissions_count - 3 }} {{ __('more') }}</span>
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <small class="text-muted">
                                    {{ __('Created') }} {{ $role->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.show', $role) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ __('View') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    @if(!in_array($role->name, ['super_admin', 'admin', 'user', 'guest']))
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="btn btn-sm btn-info"
                                       title="{{ __('Edit') }}">
                                        <i class="ti ti-edit"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                data-confirm-delete
                                                title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="d-flex mt-4">
            <p class="m-0 text-muted">
                {{ __('Showing') }}
                <span>{{ $roles->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $roles->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $roles->total() }}</span>
                {{ __('entries') }}
            </p>
            <ul class="pagination ms-auto">
                {{ $roles->links() }}
            </ul>
        </div>
    @endif
@endsection
