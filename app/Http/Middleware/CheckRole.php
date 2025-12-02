<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = auth()->user();

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            // If AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. You do not have permission to access this resource.',
                ], 403);
            }

            // If web request, abort with 403
            abort(403, 'Unauthorized action. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}
