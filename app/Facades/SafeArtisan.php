<?php

namespace App\Facades;

use Illuminate\Support\Facades\Artisan as BaseArtisan;

class SafeArtisan extends BaseArtisan
{
    public static function call($command, $parameters = [], $outputBuffer = null)
    {
        // Detect if the call is being made from a route
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        foreach ($backtrace as $trace) {
            if (isset($trace['file']) && strpos($trace['file'], 'routes') !== false) {
                throw new \RuntimeException('Artisan::call is not allowed in routes.');
            }
        }

        return parent::call($command, $parameters, $outputBuffer);
    }
}