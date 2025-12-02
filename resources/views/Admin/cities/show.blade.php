@extends('layouts.admin')

@section('title', __('City Details') . ' - ' . $city->name)

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.cities.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Cities') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-building-community"></i> {{ $city->name }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <!-- City Information -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-status-top bg-{{ $city->is_active ? 'success' : 'danger' }}"></div>

                <div class="card-body text-center">
                    <span class="avatar avatar-xl bg-blue-lt mb-3">
                        <i class="ti ti-building-community fs-1"></i>
                    </span>
                    <h3 class="m-0 mb-1">{{ $city->name }}</h3>
                    <div class="text-muted mb-3">{{ __('City') }} #{{ $city->id }}</div>

                    <div class="mb-3">
                        <span class="badge bg-{{ $city->is_active ? 'success' : 'danger' }} badge-lg">
                            {{ $city->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">{{ __('City Statistics') }}</h4>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-muted small">{{ __('Total Addresses') }}</div>
                                <div class="h2 mb-0">{{ $city->addresses_count }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">{{ __('Status') }}</div>
                                <div class="h2 mb-0">
                                    @if($city->is_active)
                                        <i class="ti ti-check text-success"></i>
                                    @else
                                        <i class="ti ti-x text-danger"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="text-muted small">{{ __('Created') }}</div>
                        <div>{{ $city->created_at->format('Y-m-d H:i') }}</div>
                        <div class="text-muted small">{{ $city->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-bolt"></i> {{ __('Quick Actions') }}
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.addresses.index', ['city_id' => $city->id]) }}" class="list-group-item list-group-item-action">
                        <i class="ti ti-map-pin text-blue"></i>
                        {{ __('View All Addresses') }}
                    </a>
                    <a href="{{ route('admin.cities.index') }}" class="list-group-item list-group-item-action">
                        <i class="ti ti-building-community text-green"></i>
                        {{ __('Back to Cities List') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Addresses in This City -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin"></i> {{ __('Recent Addresses in') }} {{ $city->name }}
                    </h3>
                    <div class="card-actions">
                        <a href="{{ route('admin.addresses.index', ['city_id' => $city->id]) }}" class="btn btn-sm btn-primary">
                            {{ __('View All') }} ({{ $city->addresses_count }})
                        </a>
                    </div>
                </div>

                @if($recentAddresses->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentAddresses as $address)
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
                                            <i class="ti ti-road"></i>
                                            {{ $address->street }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="ti ti-user"></i>
                                            <a href="{{ route('admin.users.show', $address->user) }}">
                                                {{ $address->user->name }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-muted">{{ $address->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('admin.addresses.show', $address) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($city->addresses_count > 10)
                        <div class="card-footer text-center">
                            <a href="{{ route('admin.addresses.index', ['city_id' => $city->id]) }}" class="btn btn-link">
                                {{ __('View All') }} {{ $city->addresses_count }} {{ __('Addresses') }}
                                <i class="ti ti-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                @else
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <p class="empty-title">{{ __('No addresses yet') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('No addresses have been added for this city.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
