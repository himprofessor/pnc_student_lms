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

    $userRole = Auth::user()->role ? Auth::user()->role->id : null; // Get the role ID

    if ($userRole !== (int)$role) { // Check against the role ID
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return $next($request);
}
}