<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // If not authenticated, let the auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // If authenticated but not admin, redirect to unified login
        if (!$user->isAdmin()) {
            if ($request->expectsJson()) {
                abort(403, 'Admin access required.');
            }
            
            // Logout and redirect to unified login
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('error', 'You have been logged out. Admin access required.');
        }

        return $next($request);
    }
} 