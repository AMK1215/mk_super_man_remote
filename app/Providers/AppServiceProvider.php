<?php

namespace App\Providers;

use App\Services\ApiService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ApiService::class, function ($app) {
            return new ApiService('http://gsmd.336699bet.com'); // Replace with your API base URL
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the default string length for database migrations
        Schema::defaultStringLength(191);

        // Prevent specific Artisan commands if the environment is "remote"
        if (config('app.env') === 'remote') {
            Artisan::prevent(function ($command) {
                if (in_array($command->getName(), ['migrate', 'migrate:fresh', 'db:seed'])) {
                    throw new \RuntimeException('This command is not allowed in the remote environment.');
                }
            });
        }
    }
}