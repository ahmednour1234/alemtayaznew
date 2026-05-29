<?php

namespace App\Providers;

use App\Models\Admin;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Make @can() / @cannot() / Gate::allows() work with the 'admin' guard.
        // Nullable first param = callback runs even when no web-guard user is present.
        Gate::before(function (?Authenticatable $user, string $ability): ?bool {
            $admin = auth('admin')->user();
            if (! $admin instanceof Admin) {
                return null; // no admin session → deny silently
            }
            if ($admin->isSuperAdmin()) {
                return true; // super-admin bypasses every check
            }
            return $admin->hasPermission($ability) ? true : null;
        });
    }
}

