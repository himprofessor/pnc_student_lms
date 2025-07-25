<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userRole = Auth::user()->role_id; // Using role_id directly

        if ($userRole !== (int)$role) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
    