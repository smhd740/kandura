@extends('layouts.admin')

@section('title', __('Address Details'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.addresses.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Addresses') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-map-pin"></i> {{ $address->name }}
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                @can('delete addresses')
                <form method="POST" action="{{ route('admin.addresses.destroy', $address) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" data-confirm-delete>
                        <i class="ti ti-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <!-- Address Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-status-top bg-{{ $address->is_default ? 'yellow' : 'blue' }}"></div>

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-map-pin"></i> {{ __('Address Information') }}
                    </h3>
                    @if($address->is_default)
                        <div class="card-actions">
                            <span class="badge bg-yellow badge-lg">
                                <i class="ti ti-star"></i> {{ __('Default Address') }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">{{ __('Address Name') }}</label>
                                <div class="fw-bold fs-3">{{ $address->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">{{ __('City') }}</label>
                                <div class="fw-bold fs-3">
                                    <i class="ti ti-building-community text-blue"></i>
                                    {{ $address->city->name ?? __('Unknown') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('Street Address') }}</label>
                        <div class="fw-bold">
                            <i class="ti ti-road"></i> {{ $address->street }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        @if($address->building_number)
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('Building Number') }}</label>
                            <div class="fw-bold">{{ $address->building_number }}</div>
                        </div>
                        @endif

                        @if($address->house_number)
                        <div class="col-md-6">
                            <label class="form-label text-muted">{{ __('House Number') }}</label>
                            <div class="fw-bold">{{ $address->house_number }}</div>
                        </div>
                        @endif
                    </div>

                    @if($address->details)
                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('Additional Details') }}</label>
                        <div class="alert alert-info mb-0">
                            <i class="ti ti-note"></i> {{ $address->details }}
                        </div>
                    </div>
                    @endif

                    @if($address->coordinates)
                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('GPS Coordinates') }}</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('Latitude') }}</span>
                                    <input type="text" class="form-control" value="{{ $address->coordinates['lat'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('Longitude') }}</span>
                                    <input type="text" class="form-control" value="{{ $address->coordinates['lng'] }}" readonly>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">
                            <a href="https://www.google.com/maps?q={{ $address->coordinates['lat'] }},{{ $address->coordinates['lng'] }}" target="_blank">
                                <i class="ti ti-external-link"></i> {{ __('View on Google Maps') }}
                            </a>
                        </small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label text-muted">{{ __('Full Address') }}</label>
                        <div class="alert alert-secondary mb-0">
                            {{ $address->full_address }}
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-auto">
                            <span class="text-muted">{{ __('Created') }}:</span>
                            <strong>{{ $address->created_at->format('Y-m-d H:i') }}</strong>
                            <small>({{ $address->created_at->diffForHumans() }})</small>
                        </div>
                        <div class="col-auto">
                            <span class="text-muted">{{ __('Updated') }}:</span>
                            <strong>{{ $address->updated_at->format('Y-m-d H:i') }}</strong>
                            <small>({{ $address->updated_at->diffForHumans() }})</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Owner Information -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user"></i> {{ __('Address Owner') }}
                    </h3>
                </div>

                <div class="card-body text-center">
                    <span class="avatar avatar-xl mb-3">
                        {{ strtoupper(substr($address->user->name, 0, 2)) }}
                    </span>
                    <h3 class="m-0 mb-1">
                        <a href="{{ route('admin.users.show', $address->user) }}">
                            {{ $address->user->name }}
                        </a>
                    </h3>
                    <div class="text-muted mb-3">{{ $address->user->email }}</div>

                    <div class="mb-3">
                        <span class="badge bg-{{ $address->user->role == 'super_admin' ? 'red' : ($address->user->role == 'admin' ? 'blue' : 'secondary') }}">
                            {{ __(ucfirst(str_replace('_', ' ', $address->user->role))) }}
                        </span>
                        <span class="badge bg-{{ $address->user->is_active ? 'success' : 'danger' }}">
                            {{ $address->user->is_active ? __('Active') : __('Inactive') }}
                        </span>
                    </div>

                    <a href="{{ route('admin.users.show', $address->user) }}" class="btn btn-primary w-100">
                        <i class="ti ti-user"></i> {{ __('View User Profile') }}
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-2">
                        <span class="text-muted">{{ __('Phone') }}:</span>
                        <div class="fw-bold">{{ $address->user->phone ?? __('Not provided') }}</div>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">{{ __('Total Addresses') }}:</span>
                        <div class="fw-bold">{{ $address->user->addresses->count() }}</div>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">{{ __('Member Since') }}:</span>
                        <div class="fw-bold">{{ $address->user->created_at->format('Y-m-d') }}</div>
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
                    <a href="{{ route('admin.addresses.index', ['user_id' => $address->user_id]) }}" class="list-group-item list-group-item-action">
                        <i class="ti ti-map-pin text-blue"></i>
                        {{ __('View All User Addresses') }}
                    </a>
                    <a href="{{ route('admin.addresses.index', ['city_id' => $address->city_id]) }}" class="list-group-item list-group-item-action">
                        <i class="ti ti-building-community text-green"></i>
                        {{ __('View Addresses in This City') }}
                    </a>
                    @if($address->coordinates)
                    <a href="https://www.google.com/maps?q={{ $address->coordinates['lat'] }},{{ $address->coordinates['lng'] }}" target="_blank" class="list-group-item list-group-item-action">
                        <i class="ti ti-map-2 text-red"></i>
                        {{ __('Open in Google Maps') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
