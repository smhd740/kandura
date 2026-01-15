@extends('layouts.admin')

@section('title', __('My Designs'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('My Kandura Designs') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Create and manage your custom kandura designs') }}</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('user.designs.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('Create New Design') }}
            </a>
        </div>
    </div>
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('My Designs') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $totalDesigns ?? 0 }}</div>
                        <div class="badge bg-primary-lt">{{ __('designs') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Images') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $totalImages ?? 0 }}</div>
                        <div class="badge bg-azure-lt">{{ __('images') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Avg. Price') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ number_format($avgPrice ?? 0, 2) }}</div>
                        <div class="badge bg-green-lt">{{ __('SYP') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Latest Design') }}</div>
                    </div>
                    <div class="text-truncate">
                        <strong>{{ $latestDesign ?? __('None') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('user.designs.index') }}" class="row g-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('Search by name or description...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter by Size --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('Size') }}</label>
                    <select name="size" class="form-select">
                        <option value="">{{ __('All Sizes') }}</option>
                        @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                            <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort By --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('Sort By') }}</label>
                    <select name="sort" class="form-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest First') }}</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest First') }}</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High to Low') }}</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low to High') }}</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>{{ __('Name A-Z') }}</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i>
                        </button>
                        @if(request()->hasAny(['search', 'size', 'sort']))
                            <a href="{{ route('user.designs.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Designs Grid --}}
    <div class="row row-cards">
        @forelse($designs as $design)
        <div class="col-sm-6 col-lg-4">
            <div class="card card-sm">
                {{-- Design Image --}}
                <a href="{{ route('user.designs.show', $design) }}" class="d-block">
                    @if($design->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $design->images->first()->image_path) }}"
                             class="card-img-top"
                             alt="{{ $design->getTranslation('name', app()->getLocale()) }}"
                             style="height: 250px; object-fit: cover;">
                    @else
                        <div class="card-img-top" style="height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="ti ti-photo" style="font-size: 4rem; color: rgba(255,255,255,0.5);"></i>
                        </div>
                    @endif
                </a>

                <div class="card-body">
                    {{-- Design Name --}}
                    <h3 class="card-title">
                        <a href="{{ route('user.designs.show', $design) }}" class="text-reset">
                            {{ $design->getTranslation('name', app()->getLocale()) }}
                        </a>
                    </h3>

                    {{-- Description --}}
                    <p class="text-muted">
                        {{ Str::limit($design->getTranslation('description', app()->getLocale()), 80) }}
                    </p>

                    {{-- Price --}}
                    <div class="d-flex align-items-center mb-2">
                        <div class="h3 mb-0 text-primary">{{ number_format($design->price, 2) }} {{ __('SYP') }}</div>
                    </div>

                    {{-- Sizes --}}
                    <div class="mb-2">
                        <small class="text-muted">{{ __('Sizes') }}:</small>
                        <div class="mt-1">
                            @foreach($design->measurements as $measurement)
                                <span class="badge bg-secondary-lt me-1">{{ $measurement->size }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Card Footer with Actions --}}
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                <i class="ti ti-calendar"></i>
                                {{ $design->created_at->format('M d, Y') }}
                            </small>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group" role="group">
                                <a href="{{ route('user.designs.show', $design) }}"
                                   class="btn btn-sm btn-primary"
                                   title="{{ __('View') }}">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <a href="{{ route('user.designs.edit', $design) }}"
                                   class="btn btn-sm btn-info"
                                   title="{{ __('Edit') }}">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('user.designs.destroy', $design) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            data-confirm-delete
                                            title="{{ __('Delete') }}">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </form>
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
                            <i class="ti ti-palette"></i>
                        </div>
                        <p class="empty-title">{{ __('No designs yet') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('Start creating your custom kandura designs now!') }}
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('user.designs.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i>
                                {{ __('Create Your First Design') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($designs->hasPages())
    <div class="d-flex mt-4">
        <ul class="pagination ms-auto">
            {{ $designs->links() }}
        </ul>
    </div>
    @endif
@endsection
