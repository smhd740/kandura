@extends('layouts.admin')

@section('title', __('Sizes Management'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">
                <i class="ti ti-ruler"></i> {{ __('Sizes Management') }}
            </h2>
            <div class="text-muted mt-1">{{ __('View all available kandura sizes') }}</div>
        </div>
    </div>
@endsection

@section('content')
    {{-- Statistics Cards --}}
    <div class="row row-cards mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Sizes') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total_sizes'] }}</div>
                        <div class="badge bg-primary-lt">{{ __('sizes') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Designs') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['total_designs'] }}</div>
                        <div class="badge bg-azure-lt">{{ __('designs') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Most Popular Size') }}</div>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">{{ $stats['most_popular_size'] }}</div>
                        <div class="badge bg-green-lt">{{ __('size') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="alert alert-info mb-3">
        <div class="d-flex">
            <div>
                <i class="ti ti-info-circle icon alert-icon"></i>
            </div>
            <div>
                <h4 class="alert-title">{{ __('About Sizes') }}</h4>
                <div class="text-secondary">
                    {{ __('These sizes are fixed and pre-created in the system. Admins cannot edit or change them. Users select from these sizes when creating their kandura designs.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Sizes Grid --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                {{ __('Available Sizes') }} ({{ count($availableSizes) }})
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-4">
                @foreach($availableSizes as $size)
                <div class="col-sm-6 col-lg-4">
                    <div class="card card-sm bg-light">
                        <div class="card-body text-center">
                            {{-- Size Icon --}}
                            <div class="mb-3">
                                <span class="avatar avatar-xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 2rem; font-weight: bold;">
                                    {{ $size }}
                                </span>
                            </div>

                            {{-- Size Name --}}
                            <h3 class="card-title mb-3">
                                {{ __('Size') }}: <strong>{{ $size }}</strong>
                            </h3>

                            {{-- Stats --}}
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Designs') }}</div>
                                    <div class="h4 mb-0 text-primary">
                                        {{ $sizeStats[$size]['total_designs'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">{{ __('Users') }}</div>
                                    <div class="h4 mb-0 text-azure">
                                        {{ $sizeStats[$size]['total_users'] ?? 0 }}
                                    </div>
                                </div>
                            </div>

                            {{-- Badge --}}
                            <div class="mt-3">
                                @if($sizeStats[$size]['total_designs'] > 0)
                                    <span class="badge bg-success-lt">{{ __('In Use') }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">{{ __('Not Used Yet') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Size Guide Table --}}
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ti ti-table"></i> {{ __('Size Guide') }}
            </h3>
        </div>

        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Total Designs') }}</th>
                        <th>{{ __('Total Users') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Popularity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($availableSizes as $size)
                        @php
                            $designs = $sizeStats[$size]['total_designs'] ?? 0;
                            $users = $sizeStats[$size]['total_users'] ?? 0;
                            $maxDesigns = max(array_column($sizeStats, 'total_designs'));
                            $popularity = $maxDesigns > 0 ? round(($designs / $maxDesigns) * 100) : 0;
                        @endphp
                        <tr>
                            {{-- Size Badge --}}
                            <td>
                                <span class="badge bg-primary" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                    {{ $size }}
                                </span>
                            </td>

                            {{-- Designs Count --}}
                            <td>
                                <strong>{{ $designs }}</strong> {{ __('designs') }}
                            </td>

                            {{-- Users Count --}}
                            <td>
                                <strong>{{ $users }}</strong> {{ __('users') }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($designs > 0)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>

                            {{-- Popularity Bar --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary"
                                                 role="progressbar"
                                                 style="width: {{ $popularity }}%"
                                                 aria-valuenow="{{ $popularity }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-muted ms-2">{{ $popularity }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
