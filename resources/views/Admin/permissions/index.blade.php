@extends('layouts.admin')

@section('title', __('Permissions Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-key"></i> {{ __('Permissions Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Manage system permissions') }}</div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Permissions') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total'] }}</div>
                        <div class="me-auto">
                            <span class="text-green d-inline-flex align-items-center lh-1">
                                <i class="ti ti-key fs-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Modules') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['modules'] }}</div>
                        <div class="me-auto">
                            <span class="text-blue d-inline-flex align-items-center lh-1">
                                <i class="ti ti-folder fs-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions by Module -->
    @foreach($permissions as $module => $modulePermissions)
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="ti ti-folder text-blue"></i>
                    {{ __(ucfirst($module)) }}
                </h3>
                <div class="card-actions">
                    <span class="badge bg-blue">{{ $modulePermissions->count() }} {{ __('permissions') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($modulePermissions as $permission)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span class="avatar bg-blue-lt">
                                                <i class="ti ti-key"></i>
                                            </span>
                                        </div>
                                        <div class="flex-fill">
                                            <div class="fw-bold">{{ $permission->name }}</div>
                                            <div class="text-muted small">
                                                {{ __('Created') }} {{ $permission->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($permissions->count() == 0)
        <div class="card">
            <div class="card-body">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-key"></i>
                    </div>
                    <p class="empty-title">{{ __('No permissions found') }}</p>
                    <p class="empty-subtitle text-muted">
                        {{ __('No permissions available in the system.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
@endsection
