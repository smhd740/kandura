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
                    <h3 class="card-title">{{ __('Design Images') }}</h3>
                    <div class="card-actions">
                        <span class="badge bg-primary">{{ $design->images->count() }} {{ __('images') }}</span>
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

                                {{-- Image Modal --}}
                                <div class="modal modal-blur fade" id="imageModal{{ $image->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
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
                        </div>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Description') }}</h3>
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
                    <h3 class="card-title">{{ __('Design Options') }}</h3>
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
                                            <span class="badge bg-primary">
                                                {{ $option->getTranslation('name', app()->getLocale()) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
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
                    <h3 class="card-title">{{ __('Price') }}</h3>
                </div>
                <div class="card-body text-center">
                    <div class="display-3 fw-bold text-primary">
                        {{ number_format($design->price, 2) }}
                    </div>
                    <div class="text-muted">{{ __('Syrian Pounds') }}</div>
                </div>
            </div>

            {{-- Sizes Card --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Available Sizes') }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($design->measurements as $measurement)
                            <span class="badge bg-secondary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ $measurement->size }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Creator Info --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Creator') }}</h3>
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
                    <a href="{{ route('admin.users.show', $design->user) }}" class="btn btn-primary w-100">
                        <i class="ti ti-user"></i> {{ __('View Profile') }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Design Info --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Design Information') }}</h3>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="row align-items-center">
                            <div class="col text-muted">
                                <i class="ti ti-calendar"></i> {{ __('Created') }}
                            </div>
                            <div class="col-auto">
                                <strong>{{ $design->created_at->format('M d, Y') }}</strong>
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
