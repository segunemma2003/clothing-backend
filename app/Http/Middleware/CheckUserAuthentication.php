<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // The user is authenticated, proceed with the request
            return $next($request);
        }

        // The user is not authenticated, return a response indicating this
        return response()->json([
            'status' => 'error',
            'message' => 'User is not logged in.',
        ], 401); // 401 Unauthorized status code
    }
}
