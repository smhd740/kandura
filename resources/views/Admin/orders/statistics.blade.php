@extends('layouts.admin')

@section('title', __('Orders Statistics'))

@section('header')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ route('admin.orders.index') }}" class="text-muted">
                    <i class="ti ti-arrow-left"></i> {{ __('Back to Orders') }}
                </a>
            </div>
            <h2 class="page-title">
                <i class="ti ti-chart-bar"></i> {{ __('Orders Statistics') }}
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-deck row-cards">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-primary">{{ $stats['total_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['total_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Pending Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-yellow">{{ $stats['pending_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['pending_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Processing Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-cyan">{{ $stats['processing_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['processing_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Completed Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-success">{{ $stats['completed_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['completed_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Cancelled Orders') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-danger">{{ $stats['cancelled_orders'] }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">{{ $stats['cancelled_orders'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Total Revenue') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-success">${{ number_format($stats['total_revenue'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="subheader">{{ __('Average Order Value') }}</div>
                        <div class="ms-auto lh-1">
                            <span class="badge bg-info">${{ number_format($stats['average_order_value'], 2) }}</span>
                        </div>
                    </div>
                    <div class="h1 mb-0 mt-2">${{ number_format($stats['average_order_value'], 2) }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
