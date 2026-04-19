<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        if (!in_array($request->user()->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden - insufficient role'
            ], 403);
        }

        return $next($request);
    }
}