<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Kandura Store') }}</title>

    <!-- Tabler CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet">

    <!-- Tabler Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #206bc4;
            --success-color: #2fb344;
            --danger-color: #d63939;
            --warning-color: #f76707;
            --info-color: #4299e1;
        }

        /* Smooth Transitions */
        * {
            transition: all 0.2s ease-in-out;
        }

        /* Sidebar Active State */
        .navbar-vertical .nav-item.active .nav-link {
            background: linear-gradient(90deg, var(--primary-color) 0%, #1a5294 100%);
            color: white !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(32, 107, 196, 0.3);
        }

        .navbar-vertical .nav-link:hover {
            background: rgba(32, 107, 196, 0.1);
            transform: translateX({{ app()->getLocale() == 'ar' ? '-' : '' }}5px);
        }

        /* Cards with Hover Effect */
        .card {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,.05);
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,.1);
            transform: translateY(-2px);
        }

        /* Stat Cards */
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5294 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Badges */
        .badge {
            padding: 0.35rem 0.65rem;
            font-weight: 600;
            border-radius: 6px;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,.15);
        }

        /* Avatar */
        .avatar {
            border-radius: 50%;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color), #4299e1);
        }

        /* Dropdown */
        .dropdown-menu {
            border-radius: 12px;
            border: none;
            box-shadow: 0 8px 24px rgba(0,0,0,.12);
            margin-top: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            margin: 0.25rem 0.5rem;
            padding: 0.5rem 0.75rem;
        }

        .dropdown-item:hover {
            background: rgba(32, 107, 196, 0.1);
            transform: translateX({{ app()->getLocale() == 'ar' ? '-' : '' }}3px);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Table */
        .table > :not(caption) > * > * {
            padding: 1rem 0.75rem;
        }

        .table tbody tr:hover {
            background: rgba(32, 107, 196, 0.05);
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(0,0,0,.05);
            padding: 1.5rem 0;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.25rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #1a5294;
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        /* RTL Adjustments */
        [dir="rtl"] .navbar-vertical {
            right: 0;
            left: auto;
        }

        [dir="rtl"] .page-wrapper {
            margin-right: 15rem;
            margin-left: 0;
        }
    </style>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>

    @stack('styles')
</head>
<body>
    <div class="page">
        <!-- Sidebar -->
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Brand -->
                <h1 class="navbar-brand navbar-brand-autodark">
<a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none" style="color: #1a5294;">                        <i class="ti ti-building-store fs-1 me-2"></i>
                        <span class="fs-4 fw-bold">{{ __('Kandura Store') }}</span>
                    </a>
                </h1>

                <!-- Mobile User Menu -->
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                            <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="ti ti-logout icon"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <!-- Dashboard -->
                        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-home fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Dashboard') }}</span>
                            </a>
                        </li>

                        <!-- Users (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-users fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Users') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- Cities (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.cities.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-building-community fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Cities') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- Addresses (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.addresses.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.addresses.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-map-pin fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Addresses') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- Divider -->
                        <li class="nav-item">
                            <div class="hr-text my-3">{{ __('Kandura Management') }}</div>
                        </li>

                        <!-- Design Options (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.design-options.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.design-options.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-palette fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Design Options') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- Measurements (Sizes) (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.measurements.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.measurements.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-ruler fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Sizes') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- All Designs (Admin & Super Admin only) -->
                        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                        <li class="nav-item {{ request()->routeIs('admin.designs.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.designs.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-shirt fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('All Designs') }}</span>
                            </a>
                        </li>
                        @endif

                        <!-- Roles & Permissions (Super Admin Only) -->
@if(auth()->user()->role === 'super_admin')
<li class="nav-item">
    <div class="hr-text my-3">{{ __('Access Control') }}</div>
</li>

<li class="nav-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.roles.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-shield-lock fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Roles') }}</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.permissions.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-key fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Permissions') }}</span>
    </a>
</li>

<li class="nav-item {{ request()->routeIs('admin.users.create') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.users.create') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-user-plus fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Create Admin') }}</span>
    </a>
</li>
@endif



                        <!-- Add this section after "Kandura Management" divider and before "System" divider -->

<!-- Divider -->
<li class="nav-item">
    <div class="hr-text my-3">{{ __('Orders & Sales') }}</div>
</li>

<!-- Orders (Admin & Super Admin only) -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin']))
<li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.orders.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-shopping-cart fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Orders') }}</span>
    </a>
</li>
@endif

<!-- Coupons (Admin & Super Admin only) -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin']))
<li class="nav-item {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.coupons.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-discount-2 fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Coupons') }}</span>
    </a>
</li>
@endif

<!-- Wallets (Admin & Super Admin only) -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin']))
<li class="nav-item {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.wallets.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-wallet fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Wallets') }}</span>
    </a>
</li>
@endif

<!-- Transactions (Admin & Super Admin only) -->
@if(in_array(auth()->user()->role, ['admin', 'super_admin']))
<li class="nav-item {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.transactions.index') }}">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            <i class="ti ti-receipt fs-2"></i>
        </span>
        <span class="nav-link-title">{{ __('Transactions') }}</span>
    </a>
</li>
@endif


                        <!-- Divider -->
                        <li class="nav-item">
                            <div class="hr-text my-3">{{ __('System') }}</div>
                        </li>




                        <!-- Settings -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.edit') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-settings fs-2"></i>
                                </span>
                                <span class="nav-link-title">{{ __('Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Page Header -->
            <header class="navbar navbar-expand-md d-print-none">
                <div class="container-xl">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    {{-- <div class="navbar-nav flex-row order-md-last">
                        <!-- Language Switcher -->
                        <div class="nav-item dropdown me-2">
                            <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" aria-label="Language">
                                <i class="ti ti-language fs-2"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('locale.switch', 'ar') }}" class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                    🇸🇾 العربية
                                </a>
                                <a href="{{ route('locale.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                    🇬🇧 English
                                </a>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                                <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                <div class="d-none d-xl-block ps-2">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    <div class="mt-1 small text-muted">
                                        <span class="badge bg-primary-lt">{{ __(ucfirst(auth()->user()->role)) }}</span>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="ti ti-user icon"></i> {{ __('Profile') }}
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-settings icon"></i> {{ __('Settings') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ti ti-logout icon"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div> --}}



                    <div class="navbar-nav flex-row order-md-last align-items-center">
                        <!-- Language Switcher -->
                        <div class="nav-item dropdown me-3">
                            <a href="#" class="nav-link px-0 d-flex align-items-center" data-bs-toggle="dropdown" aria-label="Language">
                                <i class="ti ti-language fs-2"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="{{ route('locale.switch', 'ar') }}" class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}">
                                    🇸🇾 العربية
                                </a>
                                <a href="{{ route('locale.switch', 'en') }}" class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                                    🇬🇧 English
                                </a>
                            </div>
                        </div>

                        <!-- Notifications Bell -->
                        <div class="nav-item dropdown me-3">
                            <a href="#" class="nav-link px-0 d-flex align-items-center position-relative" data-bs-toggle="dropdown" aria-label="Notifications" id="notificationBell">
                                <i class="ti ti-bell fs-2"></i>
                                <span class="badge bg-danger badge-notification badge-blink d-none" id="notificationBadge" style="position: absolute; top: -4px; right: -6px; font-size: 0.65rem; min-width: 16px; height: 16px; padding: 0 4px; line-height: 16px; border-radius: 50%;">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 360px; max-height: 420px; overflow: hidden;" id="notificationDropdown">
                                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="ti ti-bell me-1"></i> {{ __('Notifications') }}
                                    </h6>
                                    <a href="#" class="text-muted small" id="markAllRead" style="display: none;">
                                        {{ __('Mark all read') }}
                                    </a>
                                </div>
                                <div id="notificationList" style="max-height: 320px; overflow-y: auto;">
                                    <div class="text-center text-muted py-4" id="noNotifications">
                                        <i class="ti ti-bell-off fs-1 d-block mb-2" style="opacity: 0.3;"></i>
                                        <span class="small">{{ __('No new notifications') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link d-flex lh-1 text-reset p-0 align-items-center" data-bs-toggle="dropdown" aria-label="Open user menu">
                                <span class="avatar avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                <div class="d-none d-xl-block ps-2">
                                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                                    {{-- <div class="mt-1 small text-muted">
                                        <span class="badge bg-primary-lt">{{ __(ucfirst(auth()->user()->role)) }}</span>
                                    </div> --}}
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                                    <i class="ti ti-user icon"></i> {{ __('Profile') }}
                                </a>
                                <a href="#" class="dropdown-item">
                                    <i class="ti ti-settings icon"></i> {{ __('Settings') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="ti ti-logout icon"></i> {{ __('Logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Header Section -->
            @hasSection('header')
                <div class="page-header">
                    <div class="container-xl">
                        @yield('header')
                    </div>
                </div>
            @endif

            <!-- Page Body -->
            <div class="page-body">
                <div class="container-xl">
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-check icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">{{ __('Success!') }}</h4>
                                    <div class="text-secondary">{{ session('success') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">{{ __('Error!') }}</h4>
                                    <div class="text-secondary">{{ session('error') }}</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <i class="ti ti-alert-circle icon alert-icon"></i>
                                </div>
                                <div>
                                    <h4 class="alert-title">{{ __('Validation Error!') }}</h4>
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Main Content -->
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer footer-transparent d-print-none mt-auto">
                <div class="container-xl">
                    <div class="row text-center align-items-center">
                        <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                            <ul class="list-inline list-inline-dots mb-0">
                                <li class="list-inline-item">
                                    &copy; {{ date('Y') }}
                                    <a href="{{ url('/') }}" class="link-secondary">{{ config('app.name') }}</a>.
                                    {{ __('All rights reserved.') }}
                                </li>
                                <li class="list-inline-item">
                                    <a href="#" class="link-secondary">{{ __('Version') }} 1.0.0</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Tabler Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Confirm delete actions
        document.querySelectorAll('[data-confirm-delete]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                if (!confirm('{{ __("Are you sure you want to delete this item?") }}')) {
                    e.preventDefault();
                }
            });
        });
    </script>

    @stack('scripts')

    <!-- Firebase Notifications -->
<script>
console.log('🔥 Firebase script starting...');

// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyA-JUGF6cA5c_c_CMvh2X-deEyeP_WlxWk",
    authDomain: "kandura-store-notifications.firebaseapp.com",
    projectId: "kandura-store-notifications",
    storageBucket: "kandura-store-notifications.firebasestorage.app",
    messagingSenderId: "845537116493",
    appId: "1:845537116493:web:1e31619cb1f4d5e1863704",
    measurementId: "G-D7FR13Z75C"
};

// Initialize Firebase
try {
    firebase.initializeApp(firebaseConfig);
    console.log('✅ Firebase initialized');
} catch (error) {
    console.error('❌ Firebase init error:', error);
}

const messaging = firebase.messaging();
const VAPID_KEY = 'BJZZNUEPjd09mmI2cqB43iyk0cpfF3P2TXX3iBUaYKRAWdJAR-XOtgvw-CM6KsXrU1sjvOzoLtSyRZhD3Tw7H14';

@php
$token = auth()->user()->createToken('browser-' . time());
@endphp
const authToken = '{{ $token->plainTextToken }}';
localStorage.setItem('auth_token', authToken);

// Request permission and get token
function enableNotifications() {
    console.log('📢 Requesting notification permission...');

    Notification.requestPermission().then((permission) => {
        console.log('Permission result:', permission);

        if (permission === 'granted') {
            console.log('✅ Permission granted! Getting FCM token...');

            messaging.getToken({ vapidKey: VAPID_KEY })
                .then((currentToken) => {
                    if (currentToken) {
                        console.log('✅ FCM Token:', currentToken);
                        saveTokenToBackend(currentToken);
                    } else {
                        console.log('❌ No FCM token available');
                    }
                })
                .catch((err) => {
                    console.error('❌ Error getting token:', err);
                });
        } else {
            console.log('❌ Permission denied');
        }
    });
}

// Save token to backend
function saveTokenToBackend(token) {
    console.log('💾 Saving token to backend...');

    fetch('/api/device-tokens', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            token: token,
            device_type: 'web',
            device_name: 'Admin Dashboard'
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('✅ Token saved:', data);
    })
    .catch(err => console.error('❌ Save error:', err));
}

// Handle foreground messages
// Handle foreground messages
messaging.onMessage((payload) => {
    console.log('📬 Foreground message received:', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        data: payload.data || {}
    };

    // Show notification in foreground
    const notification = new Notification(notificationTitle, notificationOptions);

    // Handle click
    notification.onclick = function(event) {
        event.preventDefault();
        const data = payload.data || {};
        const type = data.type;
        let url = '/dashboard';

        if (type === 'new_order_admin' || type === 'new_order_user') {
            const orderId = data.order_id;
            url = orderId ? `/admin/orders/${orderId}` : '/admin/orders';
        } else if (type === 'design_created') {
            const designId = data.design_id;
            url = designId ? `/admin/designs/${designId}` : '/admin/designs';
        } else if (type === 'order_status_changed') {
            const orderId = data.order_id;
            url = orderId ? `/admin/orders/${orderId}` : '/admin/orders';
        }

        window.location.href = url;
        notification.close();
    };
});


// ===== NOTIFICATION BELL SYSTEM =====
function loadNotifications() {
    // Load unread count
    fetch('/api/notifications/unread-count', {
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationBadge(data.data.unread_count);
        }
    })
    .catch(err => console.error('Error loading unread count:', err));

    // Load notification list
    fetch('/api/notifications?per_page=15', {
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('📋 Notifications API response:', data);
        if (data.success && data.data && data.data.data) {
            renderNotifications(data.data.data);
        }
    })
    .catch(err => console.error('Error loading notifications:', err));
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    const markAllBtn = document.getElementById('markAllRead');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.classList.remove('d-none');
        if (markAllBtn) markAllBtn.style.display = 'inline';
    } else {
        badge.classList.add('d-none');
        if (markAllBtn) markAllBtn.style.display = 'none';
    }
}

function renderNotifications(notifications) {
    const list = document.getElementById('notificationList');
    const noNotif = document.getElementById('noNotifications');

    if (!notifications || notifications.length === 0) {
        noNotif.style.display = 'block';
        return;
    }

    noNotif.style.display = 'none';
    let html = '';

    notifications.forEach(function(notif) {
        const notifData = notif.data || {};
        const isUnread = !notif.read_at;
        const bgClass = isUnread ? 'background: rgba(32, 107, 196, 0.05);' : '';

        let icon = 'ti-bell';
        let iconColor = 'bg-primary';
        const notifType = notifData.type || '';

        if (notifType === 'design_created') {
            icon = 'ti-palette';
            iconColor = 'bg-success';
        } else if (notifType === 'new_order_admin' || notifType === 'new_order_user') {
            icon = 'ti-shopping-cart';
            iconColor = 'bg-warning';
        } else if (notifType === 'order_status_changed') {
            icon = 'ti-refresh';
            iconColor = 'bg-info';
        }

        const createdAt = notif.created_at ? new Date(notif.created_at).toLocaleString() : '';
        const title = notifData.title || notifData.message || 'Notification';
        const body = notifData.body || notifData.description || '';
        const orderId = notifData.order_id || '';
        const designId = notifData.design_id || '';

        html += `
            <a href="#" class="dropdown-item d-flex align-items-start py-2 px-3" style="${bgClass} border-bottom: 1px solid rgba(0,0,0,.05);" onclick="markAsRead(event, '${notif.id}', '${notifType}', '${orderId}', '${designId}')">
                <span class="avatar avatar-sm ${iconColor} text-white me-2 mt-1" style="min-width: 32px; width: 32px; height: 32px;">
                    <i class="ti ${icon}" style="font-size: 14px;"></i>
                </span>
                <div class="flex-fill" style="min-width: 0;">
                    <div class="fw-bold small text-truncate">${title}</div>
                    <div class="text-muted small text-truncate">${body}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">${createdAt}</div>
                </div>
                ${isUnread ? '<span class="ms-2 mt-2"><span class="badge bg-primary" style="width:8px; height:8px; padding:0; border-radius:50%;"></span></span>' : ''}
            </a>
        `;
    });

    list.innerHTML = html;
}

function markAsRead(event, notifId, type, orderId, designId) {
    event.preventDefault();

    fetch('/api/notifications/' + notifId + '/read', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        let url = '/dashboard';
        if ((type === 'new_order_admin' || type === 'new_order_user' || type === 'order_status_changed') && orderId) {
            url = '/admin/orders/' + orderId;
        } else if (type === 'design_created' && designId) {
            url = '/admin/designs/' + designId;
        }
        window.location.href = url;
    })
    .catch(() => {
        window.location.href = '/dashboard';
    });
}

document.getElementById('markAllRead')?.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    fetch('/api/notifications/read-all', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + authToken,
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => loadNotifications())
    .catch(err => console.error('Error:', err));
});

// Load notifications on page load
setTimeout(() => loadNotifications(), 2000);

// Refresh notifications every 30 seconds
setInterval(() => loadNotifications(), 30000);

// Reload notifications when bell is clicked
document.getElementById('notificationBell')?.addEventListener('click', function() {
    loadNotifications();
});


// Register Service Worker and enable notifications
if ('serviceWorker' in navigator) {
    console.log('🔧 Registering Service Worker...');

    navigator.serviceWorker.register('/firebase-messaging-sw.js')
        .then((registration) => {
            console.log('✅ Service Worker registered:', registration);

            // Wait a bit then enable notifications
            setTimeout(() => {
                enableNotifications();
            }, 1000);
        })
        .catch((err) => {
            console.error('❌ Service Worker error:', err);
        });
} else {
    console.log('❌ Service Worker not supported');
}
</script>
</body>
</html>
