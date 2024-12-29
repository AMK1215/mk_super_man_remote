<?php

namespace App\Providers;

use App\Services\ApiService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use App\Facades\SafeArtisan;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('artisan', function () {
        return new SafeArtisan;
    });
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     Schema::defaultStringLength(191);

    //     if (config('app.env') === 'remote' && app()->runningInConsole()) {
    //         $restrictedCommands = ['migrate', 'migrate:fresh --seed', 'db:seed', 'db:wipe', 'migrate:refresh'];
    //         $argv = request()->server('argv', []);

    //         foreach ($restrictedCommands as $command) {
    //             if (in_array($command, $argv)) {
    //                 throw new \RuntimeException("The '{$command}' command is not allowed in the remote environment.");
    //             }
    //         }
    //     }
    // }

//     public function boot(): void
// {
//     Schema::defaultStringLength(191);

//     if (config('app.env') === 'remote' && app()->runningInConsole()) {
//         $restrictedCommands = ['migrate', 'migrate:fresh', 'migrate:fresh --seed', 'db:seed', 'db:wipe', 'migrate:refresh'];

//         // Use the global $argv variable directly
//         global $argv;

//         foreach ($restrictedCommands as $command) {
//             if (in_array($command, $argv)) {
//                 throw new \RuntimeException("The '{$command}' command is not allowed in the remote environment.");
//             }
//         }
//     }
// }

    // public function boot(): void
    // {
    //     // Set the default string length for database migrations
    //     Schema::defaultStringLength(191);

    //     // Prevent specific Artisan commands if the environment is "remote"
    //     if (config('app.env') === 'remote') {
    //         Artisan::prevent(function ($command) {
    //             if (in_array($command->getName(), ['migrate', 'migrate:fresh', 'db:seed'])) {
    //                 throw new \RuntimeException("The '{$command->getName()}' command is not allowed in the remote environment.");
    //             }
    //         });
    //     }
    // }

    public function boot(): void
{
    if (app()->runningInConsole()) {
        $restrictedCommands = [
            'migrate',
            'migrate:fresh',
            'migrate:install',
            'migrate:rollback',
            'migrate:reset',
            'migrate:refresh',
            'db:wipe',
            'db:seed',
        ];

        // Use the global $argv variable to check commands
        global $argv;

        foreach ($restrictedCommands as $command) {
            foreach ($argv as $arg) {
                if (str_contains($arg, $command)) {
                    throw new \RuntimeException("The '{$command}' command is not allowed.");
                }
            }
        }
    }
}

}