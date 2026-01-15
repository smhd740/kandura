@extends('layouts.admin')

@section('title', __('All Designs'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('All Kandura Designs') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View and manage all user designs') }}</div>
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
                        <div class="subheader">{{ __('Total Designs') }}</div>
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
                        <div class="subheader">{{ __('Total Users') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $totalCreators ?? 0 }}</div>
                        <div class="badge bg-azure-lt">{{ __('creators') }}</div>
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
                        <div class="subheader">{{ __('Total Images') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $totalImages ?? 0 }}</div>
                        <div class="badge bg-purple-lt">{{ __('images') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.designs.index') }}" class="row g-3">
                {{-- Search by Design Name --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search Design') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('Search by design name...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Search by User Name --}}
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search User') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-user"></i>
                        </span>
                        <input type="text" name="user" class="form-control"
                               placeholder="{{ __('Search by user name...') }}"
                               value="{{ request('user') }}">
                    </div>
                </div>

                {{-- Filter by Size --}}
                <div class="col-md-4">
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

                {{-- Price Range --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('Min Price') }}</label>
                    <input type="number" name="min_price" class="form-control"
                           placeholder="0"
                           value="{{ request('min_price') }}"
                           step="0.01">
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ __('Max Price') }}</label>
                    <input type="number" name="max_price" class="form-control"
                           placeholder="10000"
                           value="{{ request('max_price') }}"
                           step="0.01">
                </div>

                {{-- Filter by Design Option (Bonus) --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('Design Option') }} <span class="badge bg-success-lt ms-1">Bonus</span></label>
                    <select name="design_option" class="form-select">
                        <option value="">{{ __('All Options') }}</option>
                        @foreach($designOptions ?? [] as $option)
                            <option value="{{ $option->id }}" {{ request('design_option') == $option->id ? 'selected' : '' }}>
                                {{ $option->getTranslation('name', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit --}}
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'user', 'size', 'min_price', 'max_price', 'design_option']))
                            <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary">
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
                <a href="{{ route('admin.designs.show', $design) }}" class="d-block">
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
                        <a href="{{ route('admin.designs.show', $design) }}" class="text-reset">
                            {{ $design->getTranslation('name', app()->getLocale()) }}
                        </a>
                    </h3>

                    {{-- Description --}}
                    <p class="text-muted">
                        {{ Str::limit($design->getTranslation('description', app()->getLocale()), 100) }}
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

                    {{-- Creator Info --}}
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm me-2" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white;">
                            {{ strtoupper(substr($design->user->name, 0, 2)) }}
                        </span>
                        <div>
                            <small class="text-muted">{{ __('By') }}</small>
                            <div class="fw-bold">{{ $design->user->name }}</div>
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
                            <a href="{{ route('admin.designs.show', $design) }}" class="btn btn-primary btn-sm">
                                {{ __('View') }}
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
                            <i class="ti ti-palette"></i>
                        </div>
                        <p class="empty-title">{{ __('No designs found') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('Try adjusting your search or filter criteria.') }}
                        </p>
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
