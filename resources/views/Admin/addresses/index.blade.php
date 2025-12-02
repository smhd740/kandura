@extends('layouts.admin')

@section('title', __('Addresses Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-map-pin"></i> {{ __('Addresses Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View and manage all user addresses') }}</div>
        </div>
    </div>
@endsection

@section('content')
    <!-- Filters Card -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.addresses.index') }}" class="row g-3">
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
                               placeholder="{{ __('Search by name, city, user...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                <!-- City Filter -->
                <div class="col-md-3">
                    <label class="form-label">{{ __('City') }}</label>
                    <select name="city_id" class="form-select">
                        <option value="">{{ __('All Cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Default Filter -->
                <div class="col-md-2">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="is_default" class="form-select">
                        <option value="">{{ __('All') }}</option>
                        <option value="1" {{ request('is_default') == '1' ? 'selected' : '' }}>{{ __('Default Only') }}</option>
                        <option value="0" {{ request('is_default') == '0' ? 'selected' : '' }}>{{ __('Non-Default') }}</option>
                    </select>
                </div>

                <!-- Submit -->
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'city_id', 'is_default']))
                            <a href="{{ route('admin.addresses.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Addresses Grid -->
    <div class="row row-cards">
        @forelse($addresses as $address)
            <div class="col-md-6 col-lg-4">
                <div class="card">
                    <div class="card-status-top bg-{{ $address->is_default ? 'yellow' : 'blue' }}"></div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <h3 class="card-title">
                                    <i class="ti ti-map-pin text-primary"></i>
                                    {{ $address->name }}
                                </h3>
                                @if($address->is_default)
                                    <span class="badge bg-yellow">
                                        <i class="ti ti-star"></i> {{ __('Default') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <span class="avatar avatar-sm me-2">
                                    {{ strtoupper(substr($address->user->name, 0, 2)) }}
                                </span>
                                <div>
                                    <a href="{{ route('admin.users.show', $address->user) }}" class="text-reset fw-bold">
                                        {{ $address->user->name }}
                                    </a>
                                    <div class="text-muted small">{{ $address->user->email }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Details -->
                        <div class="text-muted mb-2">
                            <div class="mb-1">
                                <i class="ti ti-building-community text-blue"></i>
                                <strong>{{ $address->city->name ?? __('Unknown') }}</strong>
                            </div>

                            <div class="mb-1">
                                <i class="ti ti-road"></i>
                                {{ $address->street }}
                            </div>

                            @if($address->building_number || $address->house_number)
                            <div class="mb-1">
                                <i class="ti ti-building"></i>
                                @if($address->building_number)
                                    {{ __('Building') }}: {{ $address->building_number }}
                                @endif
                                @if($address->house_number)
                                    , {{ __('House') }}: {{ $address->house_number }}
                                @endif
                            </div>
                            @endif

                            @if($address->coordinates)
                            <div class="mb-1">
                                <i class="ti ti-map-2"></i>
                                <small>{{ $address->coordinates['lat'] }}, {{ $address->coordinates['lng'] }}</small>
                            </div>
                            @endif
                        </div>

                        @if($address->details)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="ti ti-note"></i> {{ Str::limit($address->details, 60) }}
                                </small>
                            </div>
                        @endif
                    </div>

                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col">
                                <small class="text-muted">
                                    {{ $address->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.addresses.show', $address) }}"
                                       class="btn btn-sm btn-primary"
                                       title="{{ __('View') }}">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    @can('delete addresses')
                                    <form method="POST" action="{{ route('admin.addresses.destroy', $address) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                data-confirm-delete
                                                title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
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
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <p class="empty-title">{{ __('No addresses found') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('Try adjusting your search or filter to find what you\'re looking for.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($addresses->hasPages())
        <div class="d-flex mt-4">
            <p class="m-0 text-muted">
                {{ __('Showing') }}
                <span>{{ $addresses->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $addresses->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $addresses->total() }}</span>
                {{ __('entries') }}
            </p>
            <ul class="pagination ms-auto">
                {{ $addresses->links() }}
            </ul>
        </div>
    @endif
@endsection
