<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminUI\RoleController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\AdminUI\DesignController;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\AdminUI\PermissionController;
use App\Http\Controllers\AdminUI\MeasurementController;
use App\Http\Controllers\AdminUI\DesignOptionController;
use App\Http\Controllers\AdminUI\UserPermissionController;
use App\Http\Controllers\AdminUI\OrderController;
use App\Http\Controllers\AdminUI\CouponController;
use App\Http\Controllers\AdminUI\WalletController;
use App\Http\Controllers\AdminUI\TransactionController;
use App\Http\Controllers\AdminUI\InvoiceController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/success', [PaymentSuccessController::class, 'handle'])->name('payment.success');


Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([
        VerifyCsrfToken::class,
    ]);

// Language Switcher
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

// Dashboard Route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
    | Addresses Management Routes
    */
        Route::controller(AddressController::class)->prefix('addresses')->name('addresses.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{address}', 'show')->name('show');
            Route::delete('/{address}', 'destroy')->name('destroy');
            Route::get('/statistics/data', 'statistics')->name('statistics');
            Route::get('/user/{user}', 'byUser')->name('by-user');
        });

        /*
    | Cities Management Routes
    */
        Route::controller(CityController::class)->prefix('cities')->name('cities.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{city}', 'show')->name('show');
            Route::get('/statistics/data', 'statistics')->name('statistics');
        });

        /*
    | Measurements (Sizes) Management Routes
    */
        Route::controller(MeasurementController::class)
            ->prefix('measurements')
            ->name('measurements.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
            });

        /*
    | Design Options Management Routes
    */
        Route::controller(DesignOptionController::class)
            ->prefix('design-options')
            ->name('design-options.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{designOption}/edit', 'edit')->name('edit');
                Route::put('/{designOption}', 'update')->name('update');
                Route::patch('/{designOption}/toggle-status', 'toggleStatus')->name('toggle-status');
                Route::delete('/{designOption}', 'destroy')->name('destroy');
            });

        /*
    | Designs Management Routes (Admin)
    */
        Route::controller(DesignController::class)
            ->prefix('designs')
            ->name('designs.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{design}', 'show')->name('show');
            });

        /*
    | Orders Management Routes
    */
        Route::controller(OrderController::class)
            ->prefix('orders')
            ->name('orders.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{order}', 'show')->name('show');
                Route::patch('/{order}/status', 'updateStatus')->name('update-status');
                Route::patch('/{order}/mark-paid', 'markAsPaid')->name('mark-paid');
                Route::get('/statistics/data', 'statistics')->name('statistics');
            });

        /*
    | Invoices Management Routes
    */
        Route::controller(InvoiceController::class)
            ->prefix('invoices')
            ->name('invoices.')
            ->group(function () {
                Route::get('/{invoice}/download', 'download')->name('download');
                Route::get('/{invoice}/view', 'view')->name('view');
            });

        /*
    | Coupons Management Routes
    */
        Route::controller(CouponController::class)
            ->prefix('coupons')
            ->name('coupons.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{coupon}/edit', 'edit')->name('edit');
                Route::put('/{coupon}', 'update')->name('update');
                Route::delete('/{coupon}', 'destroy')->name('destroy');
                Route::patch('/{coupon}/toggle-status', 'toggleStatus')->name('toggle-status');
                Route::get('/{coupon}/usages', 'usages')->name('usages');
            });

        /*
    | Wallets Management Routes
    */
        Route::controller(WalletController::class)
            ->prefix('wallets')
            ->name('wallets.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{wallet}', 'show')->name('show');
                Route::post('/{wallet}/add-balance', 'addBalance')->name('add-balance');
                Route::post('/{wallet}/deduct-balance', 'deductBalance')->name('deduct-balance');
                Route::get('/user/{user}', 'byUser')->name('by-user');
            });

        /*
    | Transactions Management Routes
    */
        Route::controller(TransactionController::class)
            ->prefix('transactions')
            ->name('transactions.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{transaction}', 'show')->name('show');
            });

        /*
    | Users - View Only (Admin & Super Admin)
    */
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{user}', 'show')->name('show')->where('user', '[0-9]+');
            Route::get('/statistics/data', 'statistics')->name('statistics');
        });

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

/*
|--------------------------------------------------------------------------
| Super Admin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | Users Management - Full Access (Super Admin Only)
        */
        Route::prefix('users')->name('users.')->group(function () {
            // Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            // Route::get('/statistics/data', [UserController::class, 'statistics'])->name('statistics');
            // Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            //Route::patch('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Roles Management
        Route::resource('roles', RoleController::class);

        // Permissions Management
        Route::get('permissions', [PermissionController::class, 'index'])
            ->name('permissions.index');

        // User Permissions
        Route::get('users/{user}/permissions', [UserPermissionController::class, 'edit'])
            ->name('users.permissions.edit');
        Route::put('users/{user}/permissions', [UserPermissionController::class, 'update'])
            ->name('users.permissions.update');
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
