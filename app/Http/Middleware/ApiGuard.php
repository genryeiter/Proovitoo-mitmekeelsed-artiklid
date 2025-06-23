<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('app.api_key');
        
        // Skip API key check if it's not set in config
        if (empty($apiKey)) {
            return $next($request);
        }

        // Check if request has valid API key
        if ($request->header('X-API-KEY') !== $apiKey) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}