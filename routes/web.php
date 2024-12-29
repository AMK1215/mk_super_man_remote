<?php

use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

require_once __DIR__.'/admin.php';

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile', [HomeController::class, 'profile'])->name('profile');

//auth routes
Route::get('/login', [LoginController::class, 'showLogin'])->name('showLogin');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('get-change-password', [LoginController::class, 'changePassword'])->name('getChangePassword');
Route::post('update-password/{user}', [LoginController::class, 'updatePassword'])->name('updatePassword');

Route::get('/test-redis', function () {
    // Store a value in Redis for 10 minutes (600 seconds)
    Cache::store('redis')->put('key', 'value', 600);

    // Retrieve the stored value
    $value = Cache::store('redis')->get('key');

    // Dump and die (dd) to output the value
    dd($value); // Should output: "value"
});

Route::get('test', function () {
    try {
        // Run the migrate:fresh --seed command
        Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true // Add --force to bypass confirmation prompt
        ]);

        return 'Database migration and seeding completed successfully.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

    Route::get('db-test', function () {
    // Define restricted commands
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

    // Get the command from the request
    $commandName = request()->get('command');

    // If the command is not provided or restricted, abort the request
    if (!$commandName) {
        return response('No command provided.', 400);
    }

    if (in_array($commandName, $restrictedCommands)) {
        abort(403, "The '{$commandName}' command is restricted.");
    }

    // Attempt to execute the command
    try {
        Artisan::call($commandName);
        return 'Command executed successfully.';
    } catch (\Exception $e) {
        return response('Error: ' . $e->getMessage(), 500);
    }
});