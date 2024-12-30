<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Artisan;
class BlockMigrateRoutes
{

    public function handle($request, Closure $next): Response
    {
        // // Detect if any migration commands are called
        // $restrictedCommands = [
        //     'migrate',
        //     'migrate:fresh',
        //     'migrate:install',
        //     'migrate:rollback',
        //     'migrate:reset',
        //     'migrate:refresh',
        //     'db:wipe',
        //     'db:seed',
        // ];

        // // Check for any Artisan::call() execution
        // foreach ($restrictedCommands as $command) {
        //     if (app()->runningInConsole() && Artisan::hasCommand($command)) {
        //         abort(403, 'Access to migration commands is restricted.');
        //     }
        // }

        return $next($request);
    }
}