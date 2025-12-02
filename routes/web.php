<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AddressController;
use App\Http\Controllers\Admin\CityController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

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
    | Users Management Routes
    */
    Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{user}', 'show')->name('show');
        Route::get('/{user}/edit', 'edit')->name('edit');
        Route::put('/{user}', 'update')->name('update');
        Route::patch('/{user}/toggle-status', 'toggleStatus')->name('toggle-status');
        Route::delete('/{user}', 'destroy')->name('destroy');
        Route::get('/statistics/data', 'statistics')->name('statistics');
    });

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
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
