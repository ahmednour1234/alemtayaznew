<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Triggers the Laravel scheduler on every HTTP request, but executes
 * at most once per minute (guarded by a cache lock).
 * Runs *after* the response is sent so users feel zero delay.
 */
class RunScheduler
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Only fire once per minute — cache key expires in 60 seconds
        if (Cache::add('scheduler_last_run', 1, 60)) {
            Artisan::call('schedule:run');
        }
    }
}
