<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Catalog\AdminCatalogController;
use App\Http\Controllers\Api\V1\Catalog\CatalogController;
use App\Http\Controllers\Api\V1\Shopping\AdminShoppingController;
use App\Http\Controllers\Api\V1\Shopping\CartController;
use App\Http\Controllers\Api\V1\Shopping\OrderController;
use App\Http\Controllers\Api\V1\Shopping\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('products', [CatalogController::class, 'products'])->name('products.index');
    Route::get('products/{product:slug}', [CatalogController::class, 'show'])->name('products.show');
    Route::get('categories', [CatalogController::class, 'categories']);
    Route::get('catalog/filters', [CatalogController::class, 'filters']);
    Route::prefix('auth')->middleware('throttle:auth')->group(function (): void {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::middleware('auth:sanctum')->prefix('auth')->group(function (): void {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('password', [AuthController::class, 'changePassword']);
        Route::delete('account', [AuthController::class, 'destroy']);
        Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');
        Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
            ->middleware('throttle:6,1');
    });

    Route::middleware('auth:sanctum')->prefix('admin')->group(function (): void {
        Route::apiResource('products', AdminCatalogController::class);
    });

    Route::middleware(['auth:sanctum', 'role:super-admin,admin'])->get('admin/ping', fn () => response()->json(['ok' => true]));

    Route::middleware(['auth:sanctum', 'role:super-admin,admin,supplier'])->prefix('admin')->group(function (): void {
        Route::get('orders', [AdminShoppingController::class, 'orders']);
        Route::get('orders/{order}', [AdminShoppingController::class, 'order']);
        Route::post('orders/{order}/transition', [AdminShoppingController::class, 'transition']);
    });

    Route::middleware(['auth:sanctum', 'role:super-admin,admin'])->prefix('admin')->group(function (): void {
        Route::get('coupons', [AdminShoppingController::class, 'coupons']);
        Route::post('coupons', [AdminShoppingController::class, 'storeCoupon']);
        Route::put('coupons/{coupon}', [AdminShoppingController::class, 'updateCoupon']);
        Route::delete('coupons/{coupon}', [AdminShoppingController::class, 'destroyCoupon']);
        Route::get('shipping-methods', [AdminShoppingController::class, 'shippingMethods']);
        Route::post('shipping-methods', [AdminShoppingController::class, 'storeShippingMethod']);
        Route::put('shipping-methods/{shippingMethod}', [AdminShoppingController::class, 'updateShippingMethod']);
        Route::delete('shipping-methods/{shippingMethod}', [AdminShoppingController::class, 'destroyShippingMethod']);
        Route::get('tax-rules', [AdminShoppingController::class, 'taxRules']);
        Route::post('tax-rules', [AdminShoppingController::class, 'storeTaxRule']);
        Route::put('tax-rules/{taxRule}', [AdminShoppingController::class, 'updateTaxRule']);
        Route::delete('tax-rules/{taxRule}', [AdminShoppingController::class, 'destroyTaxRule']);
        Route::get('payments', [AdminShoppingController::class, 'payments']);
        Route::post('payments/{payment}/capture', [AdminShoppingController::class, 'capturePayment']);
        Route::post('payments/{payment}/refund', [AdminShoppingController::class, 'refundPayment']);
        Route::get('returns', [AdminShoppingController::class, 'returns']);
        Route::patch('returns/{returnRequest}', [AdminShoppingController::class, 'updateReturn']);
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('cart', [CartController::class, 'show']);
        Route::post('cart/items', [CartController::class, 'store']);
        Route::patch('cart/items/{cartItem}', [CartController::class, 'update']);
        Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy']);
        Route::get('wishlist', [WishlistController::class, 'show']);
        Route::post('wishlist/items', [WishlistController::class, 'store']);
        Route::delete('wishlist/items/{wishlistItem}', [WishlistController::class, 'destroy']);
        Route::post('checkout', [OrderController::class, 'checkout']);
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::post('orders/{order}/payment-intent', [OrderController::class, 'paymentIntent']);
        Route::get('orders/{order}/invoice/{format}', [OrderController::class, 'invoice'])->whereIn('format', ['html', 'pdf']);
        Route::post('orders/{order}/returns', [OrderController::class, 'requestReturn']);
    });
});
