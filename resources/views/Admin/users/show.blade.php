@extends('layouts.admin')

@section('title', __('User Details') . ' - ' . $user->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.users.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Users') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-user"></i> {{ $user->name }}
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @can('edit users')
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                    <i class="ti ti-edit"></i> {{ __('Edit User') }}
                </a>
                @endcan



                @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-info">
                    <i class="ti ti-lock-access"></i> {{ __('Manage Permissions') }}
                </a>
                @endif

                @can('activate users')
                <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                        <i class="ti ti-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                    </button>
                </form>
                @endcan

                @can('delete users')
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" data-confirm-delete>
                        <i class="ti ti-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <!-- User Information Card -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="avatar avatar-xl" style="background-image: url({{ $user->profile_image_url }})">
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

                    <div class="mb-3">
                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }} badge-lg">
                            {{ $user->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{ __('Contact Information') }}</h4>
                    <div class="mb-2">
                        <div class="text-muted small">{{ __('Phone') }}</div>
                        <div>{{ $user->phone ?? __('Not provided') }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">{{ __('Email Verified') }}</div>
                        <div>
                            @if($user->email_verified_at)
                                <i class="ti ti-check text-success"></i>
                                {{ $user->email_verified_at->format('Y-m-d') }}
                            @else
                                <i class="ti ti-x text-danger"></i> {{ __('Not Verified') }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{ __('Account Details') }}</h4>
                    <div class="mb-2">
                        <div class="text-muted small">{{ __('User ID') }}</div>
                        <div>#{{ $user->id }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">{{ __('Joined') }}</div>
                        <div>{{ $user->created_at->format('Y-m-d H:i') }}</div>
                        <div class="text-muted small">{{ $stats['joined'] }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted small">{{ __('Last Updated') }}</div>
                        <div>{{ $user->updated_at->format('Y-m-d H:i') }}</div>
                        <div class="text-muted small">{{ $stats['last_updated'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats & Addresses -->
        <div class="col-lg-8">
            <!-- Statistics -->
            <div class="row row-cards mb-3">
                <div class="col-sm-6 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('Total Addresses') }}</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">{{ $stats['total_addresses'] }}</div>
                            </div>
                        </div>
                        <div id="chart-addresses" class="chart-sm"></div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">{{ __('Default Address') }}</div>
                            </div>
                            <div class="d-flex align-items-baseline">
                                <div class="h1 mb-0 me-2">
                                    @if($stats['default_address'])
                                        <i class="ti ti-check text-success"></i>
                                    @else
                                        <i class="ti ti-x text-danger"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Addresses List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin"></i> {{ __('User Addresses') }}
                    </h3>
                </div>

                @if($user->addresses->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($user->addresses as $address)
                            <div class="list-group-item">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span class="avatar bg-blue-lt">
                                            <i class="ti ti-map-pin"></i>
                                        </span>
                                    </div>
                                    <div class="col text-truncate">
                                        <div class="d-flex align-items-center">
                                            <div class="fw-bold">{{ $address->name }}</div>
                                            @if($address->is_default)
                                                <span class="badge bg-yellow ms-2">{{ __('Default') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-muted">
                                            <i class="ti ti-building-community"></i>
                                            {{ $address->city->name ?? __('Unknown') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $address->street }}
                                            @if($address->building_number)
                                                , {{ __('Building') }} {{ $address->building_number }}
                                            @endif
                                        </div>
                                        @if($address->details)
                                            <div class="text-muted small">
                                                <i class="ti ti-note"></i> {{ $address->details }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('admin.addresses.show', $address) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-eye"></i> {{ __('View') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <p class="empty-title">{{ __('No addresses yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('This user has not added any addresses.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
