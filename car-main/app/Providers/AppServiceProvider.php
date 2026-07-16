<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the Audit contract to the stub service.
        // Phase 3 will swap the implementation without touching any consuming code.
        $this->app->bind(
            \App\Contracts\AuditableInterface::class,
            \App\Services\AuditService::class
        );

        // Bind UserRepository
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        // Bind BrandRepository
        $this->app->bind(
            \App\Repositories\Contracts\BrandRepositoryInterface::class,
            \App\Repositories\Eloquent\BrandRepository::class
        );

        // Bind CarModelRepository
        $this->app->bind(
            \App\Repositories\Contracts\CarModelRepositoryInterface::class,
            \App\Repositories\Eloquent\CarModelRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Car::observe(\App\Observers\CarObserver::class);
        \App\Models\PurchaseRequest::observe(\App\Observers\PurchaseRequestObserver::class);

        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
