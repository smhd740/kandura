@extends('layouts.admin')

@section('title', __('Users Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-users"></i> {{ __('Users Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View and manage all users') }}</div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="{{ __('Search by name, email, phone...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('Role') }}</label>
                    <select name="role" class="form-select">
                        <option value="">{{ __('All Roles') }}</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ __('All Users') }} ({{ $users->total() }})
            </h3>
            <div class="card-actions">
                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.users.statistics') }}">
                                <i class="ti ti-chart-bar"></i> {{ __('View Statistics') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="ti ti-download"></i> {{ __('Export') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Contact') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Addresses') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th class="w-1">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <!-- User Info -->
                            <td>
                                <div class="d-flex py-1 align-items-center">
                                    <span class="avatar me-2">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    <div class="flex-fill">
                                        <div class="font-weight-medium">
                                            <a href="{{ route('admin.users.show', $user) }}" class="text-reset">
                                                {{ $user->name }}
                                            </a>
                                        </div>
                                        <div class="text-muted">
                                            <small>ID: {{ $user->id }}</small>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <div>{{ $user->email }}</div>
                                <div class="text-muted"><small>{{ $user->phone }}</small></div>
                            </td>

                            <!-- Role -->
                            <td>
                                <span class="badge bg-{{ $user->role == 'super_admin' ? 'red' : ($user->role == 'admin' ? 'blue' : 'secondary') }}">
                                    {{ __(ucfirst(str_replace('_', ' ', $user->role))) }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>

                            <!-- Addresses Count -->
                            <td>
                                <span class="badge bg-info">
                                    {{ $user->addresses->count() }} {{ __('addresses') }}
                                </span>
                            </td>

                            <!-- Joined Date -->
                            <td class="text-muted">
                                {{ $user->created_at->format('Y-m-d') }}
                                <small class="d-block">{{ $user->created_at->diffForHumans() }}</small>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ __('View') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    @can('edit users')
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-sm btn-info"
                                       title="{{ __('Edit') }}">
                                        <i class="ti ti-edit"></i>
                                    </a>
                                    @endcan

                                    @can('activate users')
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="btn btn-sm btn-{{ $user->is_active ? 'warning' : 'success' }}"
                                                title="{{ $user->is_active ? __('Deactivate') : __('Activate') }}">
                                            <i class="ti ti-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('delete users')
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
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
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <p class="empty-title">{{ __('No users found') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('Try adjusting your search or filter to find what you\'re looking for.') }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                {{ __('Showing') }}
                <span>{{ $users->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $users->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $users->total() }}</span>
                {{ __('entries') }}
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $users->links() }}
            </ul>
        </div>
        @endif
    </div>
@endsection
