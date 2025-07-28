<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostLoginRedirect
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only handle successful login redirects
        if ($request->isMethod('POST') && $request->routeIs('filament.*.auth.login')) {
            $user = auth()->user();
            
            if ($user) {
                // Determine where to redirect based on user's role
                if ($user->isAdmin()) {
                    return redirect('/admin');
                } elseif ($user->isEventOwner()) {
                    return redirect('/event-owner');
                } else {
                    // User has no specific role, logout and show error
                    auth()->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect('/')->with('error', 'No access permissions assigned. Please contact administrator.');
                }
            }
        }

        return $response;
    }
} 