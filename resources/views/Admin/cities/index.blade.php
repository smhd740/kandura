@extends('layouts.admin')

@section('title', __('Cities Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-building-community"></i> {{ __('Cities Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View all available cities') }}</div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cities.index') }}" class="row g-3">
                <!-- Search -->
                <div class="col-md-6">
                    <label class="form-label">{{ __('Search') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="{{ __('Search by city name...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="is_active" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'is_active']))
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cities Grid -->
    <div class="row row-cards">
        @forelse($cities as $city)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top bg-{{ $city->is_active ? 'success' : 'danger' }}"></div>

                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-blue-lt">
                                    <i class="ti ti-building-community fs-1"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="fw-bold fs-3">
                                    <a href="{{ route('admin.cities.show', $city) }}" class="text-reset">
                                        {{ $city->name }}
                                    </a>
                                </div>
                                <div class="text-muted">
                                    ID: #{{ $city->id }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Addresses') }}</div>
                                    <div class="fw-bold">
                                        <i class="ti ti-map-pin text-blue"></i>
                                        {{ $city->addresses_count }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Status') }}</div>
                                    <div>
                                        <span class="badge bg-{{ $city->is_active ? 'success' : 'danger' }}">
                                            {{ $city->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <small class="text-muted">
                                    {{ __('Added') }} {{ $city->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye"></i> {{ __('View') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-building-community"></i>
                            </div>
                            <p class="empty-title">{{ __('No cities found') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Try adjusting your search to find what you\'re looking for.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($cities->hasPages())
        <div class="d-flex mt-4">
            <p class="m-0 text-muted">
                {{ __('Showing') }}
                <span>{{ $cities->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $cities->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $cities->total() }}</span>
                {{ __('entries') }}
            </p>
            <ul class="pagination ms-auto">
                {{ $cities->links() }}
            </ul>
        </div>
    @endif
@endsection
