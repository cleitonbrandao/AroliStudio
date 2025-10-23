<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasTeam
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user has a current team assigned.
     * If not, redirects to dashboard with an error message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and has a current team
        if ($user && !$user->currentTeam) {
            // If it's an AJAX/Livewire request, return JSON response
            if ($request->wantsJson() || $request->is('livewire/*')) {
                return response()->json([
                    'error' => __('app.team_required')
                ], 403);
            }
            
            // For regular requests, redirect with flash message
            return redirect()->route('root.dashboard.hierarchy')
                ->with('error', __('app.team_required'));
        }
        
        return $next($request);
    }
}
