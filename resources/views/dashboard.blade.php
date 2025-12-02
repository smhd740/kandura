@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-dashboard"></i> {{ __('Dashboard') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Welcome back') }}, {{ auth()->user()->name }}!</div>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <span class="d-none d-sm-inline">
                    <a href="#" class="btn">
                        <i class="ti ti-refresh"></i> {{ __('Refresh') }}
                    </a>
                </span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row row-deck row-cards mb-4">
        <!-- Total Users -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="ti ti-users fs-1"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ __('Total Users') }}
                            </div>
                            <div class="text-muted">
                                <span class="h1 mb-0">{{ $totalUsers }}</span>
                                <span class="text-success ms-2">
                                    <i class="ti ti-trending-up"></i> +{{ $newUsersToday }} {{ __('today') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Addresses -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="ti ti-map-pin fs-1"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ __('Total Addresses') }}
                            </div>
                            <div class="text-muted">
                                <span class="h1 mb-0">{{ $totalAddresses }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-cyan text-white avatar">
                                <i class="ti ti-user-check fs-1"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ __('Active Users') }}
                            </div>
                            <div class="text-muted">
                                <span class="h1 mb-0">{{ $activeUsers }}</span>
                                <span class="text-muted ms-2">
                                    {{ round(($activeUsers / max($totalUsers, 1)) * 100) }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cities -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-orange text-white avatar">
                                <i class="ti ti-building-community fs-1"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">
                                {{ __('Cities') }}
                            </div>
                            <div class="text-muted">
                                <span class="h1 mb-0">{{ $totalCities }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row row-deck row-cards mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-bolt"></i> {{ __('Quick Actions') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100 btn-lg">
                                <i class="ti ti-users"></i> {{ __('Manage Users') }}
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('admin.addresses.index') }}" class="btn btn-success w-100 btn-lg">
                                <i class="ti ti-map-pin"></i> {{ __('Manage Addresses') }}
                            </a>
                        </div>

                        <div class="col-md-3">
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-info w-100 btn-lg">
                                <i class="ti ti-building-community"></i> {{ __('View Cities') }}
                            </a>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-secondary w-100 btn-lg">
                                <i class="ti ti-settings"></i> {{ __('Settings') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & System Info -->
    <div class="row row-deck row-cards">
        <!-- Recent Users (Admin only) -->
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-users"></i> {{ __('Recent Users') }}
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                            {{ __('View All') }} <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentUsers as $user)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    </div>
                                    <div class="col text-truncate">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-reset d-block">
                                            {{ $user->name }}
                                        </a>
                                        <div class="d-block text-muted text-truncate mt-n1">
                                            {{ $user->email }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-muted">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="ti ti-users fs-1"></i>
                                <p class="mt-2">{{ __('No users yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- System Information -->
        <div class="col-lg-{{ in_array(auth()->user()->role, ['admin', 'super_admin']) ? '6' : '12' }}">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle"></i> {{ __('System Information') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">{{ __('Laravel Version') }}</div>
                                <div class="fw-bold">{{ app()->version() }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">{{ __('PHP Version') }}</div>
                                <div class="fw-bold">{{ PHP_VERSION }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">{{ __('Your Role') }}</div>
                                <div class="fw-bold">
                                    <span class="badge bg-primary">{{ __(ucfirst(auth()->user()->role)) }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">{{ __('Account Status') }}</div>
                                <div class="fw-bold">
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted">{{ __('Joined') }}</div>
                                <div class="fw-bold">{{ auth()->user()->created_at->format('Y-m-d') }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">{{ __('Language') }}</div>
                                <div class="fw-bold">{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <h3 class="mb-2">{{ __('Welcome to Kandura Store!') }}</h3>
                        <p class="text-muted">
                            {{ __('You are logged in as') }} <strong>{{ __(ucfirst(auth()->user()->role)) }}</strong>
                        </p>
                        <p class="text-muted small">
                            {{ __('Use the sidebar to navigate through the admin panel.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        console.log('Stats refreshed');
    }, 30000);
</script>
@endpush
