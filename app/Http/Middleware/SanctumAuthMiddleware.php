<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SanctumAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            // Check if the user is authenticated using Sanctum
            if (!Auth::guard('sanctum')->check()) {
                Log::warning('Sanctum Middleware: Unauthenticated request', [
                    'ip' => $request->ip(),
                ]);
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            // Proceed with the request if authenticated
            return $next($request);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error'], 500);
        }
    }
}
