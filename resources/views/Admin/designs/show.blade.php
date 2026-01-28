@extends('layouts.admin')

@section('title', $design->getTranslation('name', app()->getLocale()))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.designs.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to All Designs') }}
                </a>
            </div>
            <h2 class="page-title">
                {{ $design->getTranslation('name', app()->getLocale()) }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Images Gallery --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-photo"></i> {{ __('Design Images') }}
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-primary">{{ $design->images->count() }} {{ __('image(s)') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($design->images->isNotEmpty())
                        <div class="row g-3">
                            @foreach($design->images as $image)
                            <div class="col-md-6">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     class="img-fluid rounded"
                                     alt="{{ $design->getTranslation('name', app()->getLocale()) }}"
                                     style="width: 100%; height: 300px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal{{ $image->id }}">

                                @if($image->is_primary)
                                    <span class="badge bg-success mt-2">{{ __('Primary Image') }}</span>
                                @endif

                                {{-- Image Modal --}}
                                <div class="modal modal-blur fade" id="imageModal{{ $image->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1;"></button>
                                            <div class="modal-body p-0">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                     class="img-fluid"
                                                     alt="{{ $design->getTranslation('name', app()->getLocale()) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="ti ti-photo"></i>
                            </div>
                            <p class="empty-title">{{ __('No images') }}</p>
                            <p class="empty-subtitle text-muted">
                                {{ __('This design has no images yet.') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-file-text"></i> {{ __('Description') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted" style="white-space: pre-line;">{{ $design->getTranslation('description', app()->getLocale()) }}</p>

                    @if(app()->getLocale() !== 'en' && $design->getTranslation('description', 'en'))
                        <hr>
                        <small class="text-muted">{{ __('English') }}:</small>
                        <p class="text-muted mt-2" style="white-space: pre-line;">{{ $design->getTranslation('description', 'en') }}</p>
                    @endif
                </div>
            </div>

            {{-- Design Options --}}
            @if($design->designOptions->isNotEmpty())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-settings"></i> {{ __('Design Options') }}
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-azure">{{ $design->designOptions->count() }} {{ __('option(s)') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($design->designOptions->groupBy('type') as $type => $options)
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h4 class="card-title mb-3">
                                        @switch($type)
                                            @case('color')
                                                <i class="ti ti-palette text-azure"></i> {{ __('Color') }}
                                                @break
                                            @case('fabric_type')
                                                <i class="ti ti-shirt text-green"></i> {{ __('Fabric Type') }}
                                                @break
                                            @case('sleeve_type')
                                                <i class="ti ti-hand-finger text-purple"></i> {{ __('Sleeve Type') }}
                                                @break
                                            @case('dome_type')
                                                <i class="ti ti-circle text-orange"></i> {{ __('Dome Type') }}
                                                @break
                                        @endswitch
                                    </h4>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($options as $option)
                                            <div class="d-flex align-items-center">
                                                @if($option->image)
                                                    <img src="{{ asset('storage/' . $option->image) }}"
                                                         class="avatar avatar-sm me-2"
                                                         alt="{{ $option->getTranslation('name', app()->getLocale()) }}"
                                                         style="object-fit: cover;">
                                                @endif
                                                <span class="badge bg-primary">
                                                    {{ $option->getTranslation('name', app()->getLocale()) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-settings"></i>
                        </div>
                        <p class="empty-title">{{ __('No design options') }}</p>
                        <p class="empty-subtitle text-muted">
                            {{ __('This design has no additional options selected.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Price Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-currency-dollar"></i> {{ __('Price') }}
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div class="display-3 fw-bold text-primary">
                        {{ number_format($design->price, 0) }}
                    </div>
                    <div class="text-muted">{{ __('Syrian Pounds') }}</div>
                </div>
            </div>

            {{-- Sizes Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-ruler"></i> {{ __('Available Sizes') }}
                    </h3>
                    <div class="card-actions">
                        <span class="badge bg-secondary">{{ $design->measurements->count() }} {{ __('size(s)') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($design->measurements->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($design->measurements as $measurement)
                                <span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ $measurement->size }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">{{ __('No sizes available') }}</p>
                    @endif
                </div>
            </div>

            {{-- Creator Info --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-user"></i> {{ __('Creator') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <span class="avatar avatar-lg me-3" style="background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; font-size: 1.5rem;">
                            {{ strtoupper(substr($design->user->name, 0, 2)) }}
                        </span>
                        <div>
                            <div class="fw-bold fs-4">{{ $design->user->name }}</div>
                            <div class="text-muted">{{ $design->user->email }}</div>
                        </div>
                    </div>

                    @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                    <a href="{{ route('admin.users.show', $design->user) }}" class="btn btn-azure w-100">
                        <i class="ti ti-eye"></i> {{ __('View User Profile') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Design Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="ti ti-info-circle"></i> {{ __('Design Information') }}
                    </h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-hash"></i> {{ __('Design ID') }}
                            </div>
                            <div class="col-auto">
                                <strong>#{{ $design->id }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-calendar"></i> {{ __('Created') }}
                            </div>
                            <div class="col-auto">
                                <strong>{{ $design->created_at->format('M d, Y') }}</strong>
                                <small class="d-block text-muted">{{ $design->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-clock"></i> {{ __('Last Updated') }}
                            </div>
                            <div class="col-auto">
                                <strong>{{ $design->updated_at->format('M d, Y') }}</strong>
                                <small class="d-block text-muted">{{ $design->updated_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-photo"></i> {{ __('Total Images') }}
                            </div>
                            <div class="col-auto">
                                <strong>{{ $design->images->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-settings"></i> {{ __('Design Options') }}
                            </div>
                            <div class="col-auto">
                                <strong>{{ $design->designOptions->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-power"></i> {{ __('Status') }}
                            </div>
                            <div class="col-auto">
                                @if($design->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@endpush
