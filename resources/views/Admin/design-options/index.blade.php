@extends('layouts.admin')

@section('title', __('Design Options Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-palette"></i> {{ __('Design Options Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('Manage colors, fabrics, sleeves, and dome types') }}</div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.design-options.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> {{ __('Create New Option') }}
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
                        <div class="subheader">{{ __('Colors') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $colorCount ?? 0 }}</div>
                        <div class="badge bg-azure-lt">{{ __('options') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Fabrics') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $fabricCount ?? 0 }}</div>
                        <div class="badge bg-green-lt">{{ __('options') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Sleeves') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $sleeveCount ?? 0 }}</div>
                        <div class="badge bg-purple-lt">{{ __('options') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Domes') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $domeCount ?? 0 }}</div>
                        <div class="badge bg-orange-lt">{{ __('options') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.design-options.index') }}" class="row g-3">
                {{-- Search --}}
                <div class="col-md-6">
                    <label class="form-label">{{ __('Search') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control"
                               placeholder="{{ __('Search by name...') }}"
                               value="{{ request('search') }}">
                    </div>
                </div>

                {{-- Filter by Type --}}
                <div class="col-md-3">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="color" {{ request('type') == 'color' ? 'selected' : '' }}>{{ __('Color') }}</option>
                        <option value="fabric_type" {{ request('type') == 'fabric_type' ? 'selected' : '' }}>{{ __('Fabric Type') }}</option>
                        <option value="sleeve_type" {{ request('type') == 'sleeve_type' ? 'selected' : '' }}>{{ __('Sleeve Type') }}</option>
                        <option value="dome_type" {{ request('type') == 'dome_type' ? 'selected' : '' }}>{{ __('Dome Type') }}</option>
                    </select>
                </div>

                {{-- Submit --}}
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-filter"></i> {{ __('Filter') }}
                        </button>
                        @if(request()->hasAny(['search', 'type']))
                            <a href="{{ route('admin.design-options.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> {{ __('Clear') }}
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Design Options Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ __('All Design Options') }} ({{ $designOptions->total() }})
            </h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th class="w-1">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($designOptions as $option)
                        <tr>
                            {{-- Image --}}
                            <td>
                                @if($option->image)
                                    <img src="{{ asset('storage/' . $option->image) }}"
                                         alt="{{ $option->getTranslation('name', app()->getLocale()) }}"
                                         class="avatar avatar-sm"
                                         style="object-fit: cover;">
                                @else
                                    <span class="avatar avatar-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="ti ti-palette"></i>
                                    </span>
                                @endif
                            </td>

                            {{-- Name --}}
                            <td>
                                <div class="fw-bold">{{ $option->getTranslation('name', app()->getLocale()) }}</div>
                                @if(app()->getLocale() !== 'en')
                                    <div class="text-muted small">{{ $option->getTranslation('name', 'en') }}</div>
                                @endif
                            </td>

                            {{-- Type --}}
                            <td>
                                @php
                                    $typeColors = [
                                        'color' => 'bg-azure-lt',
                                        'fabric_type' => 'bg-green-lt',
                                        'sleeve_type' => 'bg-purple-lt',
                                        'dome_type' => 'bg-orange-lt',
                                    ];
                                    $badgeClass = $typeColors[$option->type] ?? 'bg-secondary-lt';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ __(ucwords(str_replace('_', ' ', $option->type))) }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($option->is_active)
                                    <span class="badge bg-success-lt">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger-lt">{{ __('Inactive') }}</span>
                                @endif
                            </td>

                            {{-- Created At --}}
                            <td class="text-muted">
                                {{ $option->created_at->format('M d, Y') }}
                                <small class="d-block">{{ $option->created_at->diffForHumans() }}</small>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.design-options.edit', $option) }}"
                                       class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                        <i class="ti ti-edit"></i>
                                    </a>

                                    <form method="POST" action="{{ route('admin.design-options.destroy', $option) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                data-confirm-delete title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty">
                                    <div class="empty-icon">
                                        <i class="ti ti-palette"></i>
                                    </div>
                                    <p class="empty-title">{{ __('No design options found') }}</p>
                                    <p class="empty-subtitle text-muted">
                                        {{ __('Get started by creating a new design option.') }}
                                    </p>
                                    <div class="empty-action">
                                        <a href="{{ route('admin.design-options.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus"></i>
                                            {{ __('Create Design Option') }}
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($designOptions->hasPages())
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-muted">
                {{ __('Showing') }}
                <span>{{ $designOptions->firstItem() }}</span>
                {{ __('to') }}
                <span>{{ $designOptions->lastItem() }}</span>
                {{ __('of') }}
                <span>{{ $designOptions->total() }}</span>
                {{ __('entries') }}
            </p>
            <ul class="pagination m-0 ms-auto">
                {{ $designOptions->links() }}
            </ul>
        </div>
        @endif
    </div>
@endsection
