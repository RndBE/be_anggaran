<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Usage in routes: ->middleware('permission:settings.manage')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permissionSlug): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Load roles with their permissions eagerly (once per request)
        $user->loadMissing('roles.permissions');

        if (!$user->hasPermission($permissionSlug)) {
            abort(403, 'Unauthorized. You do not have the required permission: ' . $permissionSlug);
        }

        return $next($request);
    }
}
