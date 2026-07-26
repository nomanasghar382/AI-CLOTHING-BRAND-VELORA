<?php

namespace App\Providers;

use App\Contracts\InternationalShippingProviderInterface;
use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\CreatorProfileRepositoryInterface;
use App\Contracts\Repositories\LoyaltyWalletRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\WishlistRepositoryInterface;
use App\Events\SupplierInventorySynced;
use App\Listeners\RecordSecurityAudit;
use App\Listeners\RecordSupplierInventorySync;
use App\Models\Product;
use App\Observers\ProductObserver;
use App\Repositories\Eloquent\EloquentCartRepository;
use App\Repositories\Eloquent\EloquentCreatorProfileRepository;
use App\Repositories\Eloquent\EloquentLoyaltyWalletRepository;
use App\Repositories\Eloquent\EloquentProductRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\EloquentWishlistRepository;
use App\Services\Shopping\EnvShippingProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CartRepositoryInterface::class, EloquentCartRepository::class);
        $this->app->bind(WishlistRepositoryInterface::class, EloquentWishlistRepository::class);
        $this->app->bind(CreatorProfileRepositoryInterface::class, EloquentCreatorProfileRepository::class);
        $this->app->bind(LoyaltyWalletRepositoryInterface::class, EloquentLoyaltyWalletRepository::class);
        $this->app->bind(InternationalShippingProviderInterface::class, EnvShippingProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('uploads', fn (Request $request) => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('webhooks', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
        Product::observe(ProductObserver::class);
        Event::listen(SupplierInventorySynced::class, RecordSupplierInventorySync::class);
        Event::subscribe(RecordSecurityAudit::class);
    }
}
