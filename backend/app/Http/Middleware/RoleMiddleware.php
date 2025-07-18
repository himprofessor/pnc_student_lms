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

    \Log::info('User ID: ' . Auth::id() . ' Role: ' . Auth::user()->role); // Log user role

    if (!Auth::user()->hasRole($role)) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return $next($request);
}
}