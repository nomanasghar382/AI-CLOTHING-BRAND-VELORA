<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Catalog\AdminCatalogController;
use App\Http\Controllers\Api\V1\Catalog\CatalogController;
use App\Http\Controllers\Api\V1\Community\CommunityController;
use App\Http\Controllers\Api\V1\Creator\CreatorController;
use App\Http\Controllers\Api\V1\Shopping\AdminShoppingController;
use App\Http\Controllers\Api\V1\Shopping\CartController;
use App\Http\Controllers\Api\V1\Shopping\OrderController;
use App\Http\Controllers\Api\V1\Shopping\WishlistController;
use App\Http\Controllers\Api\V1\Style\StyleController;
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
        Route::apiResource('products', AdminCatalogController::class)->middleware('permission:catalog.manage');
    });

    Route::middleware(['auth:sanctum', 'role:super-admin,admin'])->get('admin/ping', fn () => response()->json(['ok' => true]));

    Route::middleware(['auth:sanctum', 'role:super-admin,admin,supplier', 'permission:orders.manage'])->prefix('admin')->group(function (): void {
        Route::get('orders', [AdminShoppingController::class, 'orders']);
        Route::get('orders/{order}', [AdminShoppingController::class, 'order']);
        Route::post('orders/{order}/transition', [AdminShoppingController::class, 'transition']);
    });

    Route::middleware(['auth:sanctum', 'role:super-admin,admin'])->prefix('admin')->group(function (): void {
        Route::get('coupons', [AdminShoppingController::class, 'coupons'])->middleware('permission:coupons.manage');
        Route::post('coupons', [AdminShoppingController::class, 'storeCoupon'])->middleware('permission:coupons.manage');
        Route::put('coupons/{coupon}', [AdminShoppingController::class, 'updateCoupon'])->middleware('permission:coupons.manage');
        Route::delete('coupons/{coupon}', [AdminShoppingController::class, 'destroyCoupon'])->middleware('permission:coupons.manage');
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

    Route::middleware(['auth:sanctum', 'permission:dashboard.view'])->prefix('admin')->group(function (): void {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::get('analytics', [AdminController::class, 'analytics']);
    });

    Route::middleware(['auth:sanctum', 'permission:reports.export'])->get('admin/reports/{report}/csv', [AdminController::class, 'exportReport'])
        ->whereIn('report', ['orders', 'customers', 'products']);

    Route::middleware(['auth:sanctum', 'permission:settings.view'])->get('admin/settings', [AdminController::class, 'settings']);
    Route::middleware(['auth:sanctum', 'permission:settings.manage'])->group(function (): void {
        Route::post('admin/settings', [AdminController::class, 'storeSetting']);
        Route::patch('admin/settings/{setting}', [AdminController::class, 'updateSetting']);
    });

    Route::middleware(['auth:sanctum', 'permission:content.view'])->group(function (): void {
        Route::get('admin/content', [AdminController::class, 'contents']);
        Route::get('admin/content/{content}', [AdminController::class, 'showContent']);
    });
    Route::middleware(['auth:sanctum', 'permission:content.manage'])->group(function (): void {
        Route::post('admin/content', [AdminController::class, 'storeContent']);
        Route::put('admin/content/{content}', [AdminController::class, 'updateContent']);
        Route::delete('admin/content/{content}', [AdminController::class, 'destroyContent']);
    });

    Route::middleware(['auth:sanctum', 'permission:customers.view'])->group(function (): void {
        Route::get('admin/customers', [AdminController::class, 'customers']);
        Route::get('admin/customers/{customer}', [AdminController::class, 'showCustomer']);
    });
    Route::middleware(['auth:sanctum', 'permission:customers.manage'])->patch('admin/customers/{customer}', [AdminController::class, 'updateCustomer']);

    Route::middleware(['auth:sanctum', 'permission:support.view'])->group(function (): void {
        Route::get('admin/support/tickets', [AdminController::class, 'tickets']);
        Route::get('admin/support/tickets/{ticket}', [AdminController::class, 'showTicket']);
    });
    Route::middleware(['auth:sanctum', 'permission:support.manage'])->group(function (): void {
        Route::patch('admin/support/tickets/{ticket}', [AdminController::class, 'updateTicket']);
        Route::post('admin/support/tickets/{ticket}/messages', [AdminController::class, 'replyTicket']);
    });

    Route::middleware(['auth:sanctum', 'permission:notifications.view'])->get('admin/notifications', [AdminController::class, 'notifications']);
    Route::middleware(['auth:sanctum', 'permission:notifications.manage'])->post('admin/notifications', [AdminController::class, 'storeNotification']);
    Route::middleware(['auth:sanctum', 'permission:activity.view'])->get('admin/activity-logs', [AdminController::class, 'activity']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('creators/{handle}', [CreatorController::class, 'show']);
        Route::get('creators/{creator}/collections', [CreatorController::class, 'collections']);
        Route::post('creators/{creator}/follow', [CreatorController::class, 'follow']);
        Route::delete('creators/{creator}/follow', [CreatorController::class, 'unfollow']);
        Route::get('creator/profile', [CreatorController::class, 'profile']);
        Route::put('creator/profile', [CreatorController::class, 'saveProfile']);
        Route::post('creator/collections', [CreatorController::class, 'storeCollection']);
        Route::put('creator/collections/{collection}', [CreatorController::class, 'updateCollection']);
        Route::delete('creator/collections/{collection}', [CreatorController::class, 'destroyCollection']);
        Route::get('creator/earnings', [CreatorController::class, 'earnings']);
        Route::post('creator/withdrawals', [CreatorController::class, 'withdrawal']);
        Route::get('lookboards', [CreatorController::class, 'lookboards']);
        Route::post('lookboards', [CreatorController::class, 'storeLookboard']);
        Route::put('lookboards/{lookboard}', [CreatorController::class, 'updateLookboard']);
        Route::delete('lookboards/{lookboard}', [CreatorController::class, 'destroyLookboard']);
        Route::get('wardrobe', [CreatorController::class, 'wardrobe']);
        Route::post('wardrobe', [CreatorController::class, 'storeWardrobe']);
        Route::get('wardrobe/recommendations', [CreatorController::class, 'wardrobeRecommendations']);
        Route::put('wardrobe/{item}', [CreatorController::class, 'updateWardrobe']);
        Route::delete('wardrobe/{item}', [CreatorController::class, 'destroyWardrobe']);
        Route::post('media/images', [CreatorController::class, 'upload']);
        Route::post('visual-searches', [CreatorController::class, 'visualSearch']);
        Route::get('visual-searches', [CreatorController::class, 'visualHistory']);
        Route::get('community/posts', [CommunityController::class, 'index']);
        Route::post('community/posts', [CommunityController::class, 'store']);
        Route::get('community/posts/{post}', [CommunityController::class, 'show']);
        Route::put('community/posts/{post}', [CommunityController::class, 'update']);
        Route::delete('community/posts/{post}', [CommunityController::class, 'destroy']);
        Route::get('community/posts/{post}/comments', [CommunityController::class, 'comments']);
        Route::post('community/posts/{post}/comments', [CommunityController::class, 'comment']);
        Route::post('community/posts/{post}/like', [CommunityController::class, 'like']);
        Route::delete('community/posts/{post}/like', [CommunityController::class, 'unlike']);
        Route::post('community/posts/{post}/bookmark', [CommunityController::class, 'bookmark']);
        Route::delete('community/posts/{post}/bookmark', [CommunityController::class, 'unbookmark']);
        Route::post('community/posts/{post}/reports', [CommunityController::class, 'report']);
        Route::post('community/posts/{post}/moderate', [CommunityController::class, 'moderate']);
        Route::get('community/activity', [CommunityController::class, 'activity']);
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

        Route::get('style/quiz', [StyleController::class, 'quiz']);
        Route::get('style/profile', [StyleController::class, 'profile']);
        Route::put('style/profile', [StyleController::class, 'updateProfile']);
        Route::put('style/body', [StyleController::class, 'updateBody']);
        Route::post('style/recommendations', [StyleController::class, 'recommend']);
        Route::post('style/outfits/occasion', [StyleController::class, 'occasion']);
        Route::post('style/chat', [StyleController::class, 'chat']);
        Route::get('style/conversations', [StyleController::class, 'conversations']);
        Route::get('style/conversations/{conversation}/messages', [StyleController::class, 'messages']);
        Route::get('style/trends', [StyleController::class, 'trends']);
        Route::get('style/saved-outfits', [StyleController::class, 'savedOutfits']);
        Route::post('style/saved-outfits', [StyleController::class, 'saveOutfit']);
        Route::post('style/feedback', [StyleController::class, 'feedback']);
    });
});
