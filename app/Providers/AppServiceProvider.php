<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Models\Admin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Loaded here as well as via composer's autoload "files" so the helpers
        // are available even when the server's autoloader has not been rebuilt.
        require_once app_path('Support/helpers.php');
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

        // Log every failed authentication attempt to the security audit trail.
        Event::listen(Failed::class, LogFailedLogin::class);
    }
}

