<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Admin\DesignController as AdminDesignController;
use App\Http\Controllers\Admin\DesignOptionController;
use App\Http\Controllers\Admin\MeasurementController as AdminMeasurementController;
use App\Http\Controllers\Api\DesignController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

//cities

Route::prefix('cities')->group(function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/{id}', [CityController::class, 'show']);
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/profile', [AuthController::class, 'profile']);
    Route::put('auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('auth/profile', [AuthController::class, 'updateProfile']);

    // Address Routes
    Route::apiResource('addresses', AddressController::class);
    // User Design Routes - Listing & Search
    Route::get('designs/my-designs', [DesignController::class, 'myDesigns']);
    Route::get('designs/browse', [DesignController::class, 'browseDesigns']);
    
    // User Design Routes
    Route::apiResource('designs', DesignController::class);


});


// Admin Routes - Stage 2


Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {

    // Design Options Management (CRUD)
    Route::apiResource('design-options', DesignOptionController::class);
    Route::post('design-options/{designOption}/toggle-active', [DesignOptionController::class, 'toggleActive'])
        ->name('admin.design-options.toggle-active');
    Route::get('design-option-types', [DesignOptionController::class, 'types'])
        ->name('admin.design-options.types');

    // Measurements (View Only)
    Route::get('measurements', [AdminMeasurementController::class, 'index'])
        ->name('admin.measurements.index');
    Route::get('measurements/{measurement}', [AdminMeasurementController::class, 'show'])
        ->name('admin.measurements.show');
    Route::get('available-sizes', [AdminMeasurementController::class, 'availableSizes'])
        ->name('admin.measurements.available-sizes');

    // Designs (View All with Filters)
    Route::get('designs', [AdminDesignController::class, 'index'])
        ->name('admin.designs.index');
    Route::get('designs/{design}', [AdminDesignController::class, 'show'])
        ->name('admin.designs.show');
    Route::get('design-statistics', [AdminDesignController::class, 'statistics'])
        ->name('admin.designs.statistics');
});
