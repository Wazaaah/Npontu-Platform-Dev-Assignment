<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Restricts access to routes that require administrator privileges.
 *
 * Applied to admin-only route groups (user management, activity templates,
 * audit log). Unauthenticated requests and authenticated non-admin users
 * both receive a 403 Forbidden response.
 *
 * Registered in bootstrap/app.php as 'admin'.
 */
class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the current session belongs to an authenticated admin.
     * Aborts with 403 if either condition is not met; otherwise passes the
     * request down the middleware stack unchanged.
     *
     * @param  Request  $request The current HTTP request.
     * @param  Closure  $next    The next middleware or controller handler.
     * @return Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
