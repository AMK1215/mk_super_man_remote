<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMigrateRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Block routes related to migrations
        $restrictedRoutes = ['migrate', 'db-test', 'migrate*'];

        foreach ($restrictedRoutes as $route) {
            if ($request->is($route)) {
                abort(403, 'Access to migration-related routes is restricted.');
            }
        }

        return $next($request);
    }
    // public function handle($request, Closure $next)
    // {
    //     if ($request->is('migrate*')) {
    //         abort(403, 'Access to migration routes is restricted.');
    //     }

    //     return $next($request);
    // }
}