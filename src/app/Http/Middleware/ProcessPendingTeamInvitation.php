<?php

namespace App\Http\Middleware;

use App\Services\TeamInvitationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProcessPendingTeamInvitation
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        protected TeamInvitationService $invitationService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('ProcessPendingTeamInvitation executed', [
            'is_authenticated' => Auth::check(),
            'has_session_invitation' => session()->has('team_invitation_id'),
            'session_invitation_id' => session('team_invitation_id'),
            'session_invitation_email' => session('team_invitation_email'),
            'user_email' => Auth::user()?->email,
            'url' => $request->url(),
        ]);
        
        // If user is authenticated and has a pending invitation in session
        if (Auth::check() && session()->has('team_invitation_id')) {
            $invitationId = session('team_invitation_id');
            $invitationEmail = session('team_invitation_email');
            $user = Auth::user();
            
            Log::info('Processing pending invitation in middleware', [
                'invitation_id' => $invitationId,
                'invitation_email' => $invitationEmail,
                'user_email' => $user->email,
            ]);
            
            // Process the invitation using the service
            $result = $this->invitationService->processPendingInvitation(
                $invitationId,
                $invitationEmail,
                $user
            );
            
            // Clear session data
            $this->invitationService->clearInvitationSession();
            
            // Flash appropriate message and redirect
            if ($result['success']) {
                session()->flash('success', $result['message']);
                Log::info('Invitation processed successfully, redirecting to dashboard');
                return redirect()->route('root.dashboard.hierarchy');
            } else {
                session()->flash('error', $result['message']);
                Log::warning('Invitation processing failed in middleware', ['reason' => $result['message']]);
            }
        }
        
        return $next($request);
    }
}
