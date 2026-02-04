<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DesignController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Admin\DesignOptionController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\Api\CouponController as ApiCouponController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\DesignController as AdminDesignController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\MeasurementController as AdminMeasurementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth Routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Cities (Public)
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


    // Notifications Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Device Tokens Routes
    Route::prefix('device-tokens')->group(function () {
        Route::get('/', [DeviceTokenController::class, 'index']);
        Route::post('/', [DeviceTokenController::class, 'store']);
        Route::delete('/{token}', [DeviceTokenController::class, 'destroy']);
    });


    // Address Routes - كل route بصلاحيته
    Route::get('addresses', [AddressController::class, 'index'])->middleware('permission:view addresses');
    Route::get('addresses/{address}', [AddressController::class, 'show'])->middleware('permission:view addresses');
    Route::post('addresses', [AddressController::class, 'store'])->middleware('permission:create addresses');
    Route::put('addresses/{address}', [AddressController::class, 'update'])->middleware('permission:edit addresses');
    Route::patch('addresses/{address}', [AddressController::class, 'update'])->middleware('permission:edit addresses');
    Route::delete('addresses/{address}', [AddressController::class, 'destroy'])->middleware('permission:delete addresses');

    // User Design Routes - كل route بصلاحيته
    Route::get('designs/my-designs', [DesignController::class, 'myDesigns'])->middleware('permission:view designs');
    Route::get('designs/browse', [DesignController::class, 'browseDesigns'])->middleware('permission:view designs');
    Route::get('designs', [DesignController::class, 'index'])->middleware('permission:view designs');
    Route::get('designs/{design}', [DesignController::class, 'show'])->middleware('permission:view designs');
    Route::post('designs', [DesignController::class, 'store'])->middleware('permission:create designs');
    Route::put('designs/{design}', [DesignController::class, 'update'])->middleware('permission:edit designs');
    Route::patch('designs/{design}', [DesignController::class, 'update'])->middleware('permission:edit designs');
    Route::delete('designs/{design}', [DesignController::class, 'destroy'])->middleware('permission:delete designs');

    // User Order Routes - كل route بصلاحيته
    Route::post('orders', [OrderController::class, 'store'])->middleware('permission:create orders');
    Route::get('orders/my-orders', [OrderController::class, 'myOrders'])->middleware('permission:view orders');
    Route::get('orders/{id}', [OrderController::class, 'show'])->middleware('permission:view orders');
    Route::post('orders/{id}/cancel', [OrderController::class, 'cancel'])->middleware('permission:cancel orders');

    // Review Routes - كل route بصلاحيته
    Route::post('reviews', [ReviewController::class, 'store'])->middleware('permission:create orders');
    Route::get('reviews', [ReviewController::class, 'index'])->middleware('permission:view orders');
    Route::get('reviews/{id}', [ReviewController::class, 'show'])->middleware('permission:view orders');

    // Wallet Routes
    Route::get('wallet/balance', [WalletController::class, 'balance'])->middleware('permission:view wallets');
    Route::get('wallet/transactions', [WalletController::class, 'transactions'])->middleware('permission:view wallet transactions');

    // Payment Methods (كل مستخدم مسجل يقدر يشوفها)
    Route::get('payment/methods', [PaymentController::class, 'paymentMethods']);

    // Process Payment for Order
    Route::post('orders/{id}/payment', [PaymentController::class, 'processPayment'])->middleware('permission:create orders');

    // Coupon Validation (كل مستخدم مسجل يقدر يفحص كوبون)
    Route::post('coupons/validate', [ApiCouponController::class, 'validate']);
});

// Admin Routes
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {

    // Design Options Management - كل route بصلاحيته
    Route::get('design-options', [DesignOptionController::class, 'index'])->middleware('permission:view design-options');
    Route::get('design-options/{designOption}', [DesignOptionController::class, 'show'])->middleware('permission:view design-options');
    Route::post('design-options', [DesignOptionController::class, 'store'])->middleware('permission:create design-options');
    Route::put('design-options/{designOption}', [DesignOptionController::class, 'update'])->middleware('permission:edit design-options');
    Route::patch('design-options/{designOption}', [DesignOptionController::class, 'update'])->middleware('permission:edit design-options');
    Route::delete('design-options/{designOption}', [DesignOptionController::class, 'destroy'])->middleware('permission:delete design-options');
    Route::post('design-options/{designOption}/toggle-active', [DesignOptionController::class, 'toggleActive'])->middleware('permission:edit design-options');
    Route::get('design-option-types', [DesignOptionController::class, 'types'])->middleware('permission:view design-options');

    // Measurements (View Only) - كل route بصلاحيته
    Route::get('measurements', [AdminMeasurementController::class, 'index'])->middleware('permission:view measurements');
    Route::get('measurements/{measurement}', [AdminMeasurementController::class, 'show'])->middleware('permission:view measurements');
    Route::get('available-sizes', [AdminMeasurementController::class, 'availableSizes'])->middleware('permission:view measurements');

    // Designs (View All with Filters) - كل route بصلاحيته
    Route::get('designs', [AdminDesignController::class, 'index'])->middleware('permission:view designs');
    Route::get('designs/{design}', [AdminDesignController::class, 'show'])->middleware('permission:view designs');
    Route::get('design-statistics', [AdminDesignController::class, 'statistics'])->middleware('permission:view designs');

    // Orders Management - كل route بصلاحيته
    Route::get('orders', [AdminOrderController::class, 'index'])->middleware('permission:view orders');
    Route::get('orders/{id}', [AdminOrderController::class, 'show'])->middleware('permission:view orders');
    Route::patch('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->middleware('permission:update order status');
    Route::get('order-statistics', [AdminOrderController::class, 'statistics'])->middleware('permission:view orders');

    // Coupons Management - كل route بصلاحيته
    Route::get('coupons', [AdminCouponController::class, 'index'])->middleware('permission:view coupons');
    Route::get('coupons/{coupon}', [AdminCouponController::class, 'show'])->middleware('permission:view coupons');
    Route::post('coupons', [AdminCouponController::class, 'store'])->middleware('permission:create coupons');
    Route::put('coupons/{coupon}', [AdminCouponController::class, 'update'])->middleware('permission:edit coupons');
    Route::patch('coupons/{coupon}', [AdminCouponController::class, 'update'])->middleware('permission:edit coupons');
    Route::delete('coupons/{coupon}', [AdminCouponController::class, 'destroy'])->middleware('permission:delete coupons');
    Route::post('coupons/{coupon}/toggle-status', [AdminCouponController::class, 'toggleStatus'])->middleware('permission:edit coupons');
    Route::get('coupon-statistics', [AdminCouponController::class, 'statistics'])->middleware('permission:view coupons');

    // Wallet Management - كل route بصلاحيته
    Route::get('wallet/{user_id}/balance', [AdminWalletController::class, 'balance'])->middleware('permission:view wallets');
    Route::get('wallet/{user_id}/transactions', [AdminWalletController::class, 'transactions'])->middleware('permission:view wallet transactions');
    Route::post('wallet/add-balance', [AdminWalletController::class, 'addBalance'])->middleware('permission:add wallet balance');
    Route::post('wallet/deduct-balance', [AdminWalletController::class, 'deductBalance'])->middleware('permission:deduct wallet balance');
});

