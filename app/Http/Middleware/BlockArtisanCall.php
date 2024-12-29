<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockArtisanCall
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for the presence of the Artisan::call method in the current route closure
        $routeAction = $request->route()->getAction();

        if (isset($routeAction['uses']) && is_callable($routeAction['uses'])) {
            $routeCode = new \ReflectionFunction($routeAction['uses']);

            // Check the route's closure code for "Artisan::call"
            $source = file_get_contents($routeCode->getFileName());
            $methodBody = substr($source, $routeCode->getStartLine(), $routeCode->getEndLine() - $routeCode->getStartLine());

            if (strpos($methodBody, 'Artisan::call') !== false) {
                abort(403, 'Artisan::call is not allowed in routes.');
            }
        }

        return $next($request);
    }

}