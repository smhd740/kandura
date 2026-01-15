<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Admin\DesignOptionController;
// الاستدعاءات الجديدة الخاصة بالدفع والمحفظة
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DesignController as AdminDesignController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\MeasurementController as AdminMeasurementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Stripe Webhook (خارج مجموعة الـ Auth وبدون middleware الحماية)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([
        VerifyCsrfToken::class,
    ]);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Cities
Route::prefix('cities')->group(function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/{id}', [CityController::class, 'show']);
});

// Protected Routes (للمستخدمين المسجلين)
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

    // User Order Routes
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/my-orders', [OrderController::class, 'myOrders']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel']);

    // --- راوتات المحفظة والدفع الجديدة (User) ---
    // Wallet Routes
    Route::prefix('wallet')->group(function () {
        Route::get('balance', [WalletController::class, 'balance']);
        Route::get('transactions', [WalletController::class, 'transactions']);
    });

    // Payment Methods
    Route::prefix('payment')->group(function () {
        Route::get('methods', [PaymentController::class, 'paymentMethods']);
    });

    // Process Payment for Order
    Route::post('orders/{id}/payment', [PaymentController::class, 'processPayment']);
});

// Admin Routes
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

    // Orders (View All with Filters)
    Route::get('orders', [AdminOrderController::class, 'index'])
        ->name('admin.orders.index');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])
        ->name('admin.orders.show');
    Route::patch('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])
        ->name('admin.orders.update-status');
    Route::get('order-statistics', [AdminOrderController::class, 'statistics'])
        ->name('admin.orders.statistics');

    // --- راوتات إدارة المحفظة (Admin) ---
    Route::prefix('wallet')->group(function () {
        Route::get('{user_id}/balance', [AdminWalletController::class, 'balance'])
            ->name('admin.wallet.balance');
        Route::post('add-balance', [AdminWalletController::class, 'addBalance'])
            ->name('admin.wallet.add-balance');
        Route::post('deduct-balance', [AdminWalletController::class, 'deductBalance'])
            ->name('admin.wallet.deduct-balance');
        Route::get('{user_id}/transactions', [AdminWalletController::class, 'transactions'])
            ->name('admin.wallet.transactions');
    });
});
