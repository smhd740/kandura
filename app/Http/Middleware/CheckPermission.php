<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        // أضف هذا السطر للتأكد إن الـ middleware شغال
        Log::info('CheckPermission middleware is working', ['permissions' => $permissions]);

        if (!auth('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user = auth('sanctum')->user();

        foreach ($permissions as $permission) {
            if ($user->hasPermissionTo($permission)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'Unauthorized. Required permission: ' . implode(' or ', $permissions),
        ], 403);
    }
}
