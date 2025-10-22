<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\TeamInvitation;
use Symfony\Component\HttpFoundation\Response;

class ProcessPendingTeamInvitation
{
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
            $teamName = session('team_invitation_team');
            
            $user = Auth::user();
            
            Log::info('Processing pending invitation', [
                'invitation_id' => $invitationId,
                'invitation_email' => $invitationEmail,
                'user_email' => $user->email,
            ]);
            
            // Find the invitation
            $invitation = TeamInvitation::find($invitationId);
            
            if (!$invitation) {
                Log::warning('Invitation not found', ['invitation_id' => $invitationId]);
                session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
                return $next($request);
            }
            
            // Validate if invitation exists and email matches
            if ($invitation->email === $invitationEmail && $invitation->email === $user->email) {
                Log::info('Emails validated, adding user to team');
                
                // Add member to team
                app(AddsTeamMembers::class)->add(
                    $invitation->team->owner,
                    $invitation->team,
                    $invitation->email,
                    $invitation->role
                );
                
                Log::info('User successfully added to team');
                
                // Delete the invitation
                $invitation->delete();
                
                // Remove from session (including url.intended to prevent redirect to expired signed URL)
                session()->forget([
                    'team_invitation_id', 
                    'team_invitation_email', 
                    'team_invitation_team',
                    'url.intended' // Remove intended URL that may have expired signature
                ]);
                
                // Add success message
                session()->flash('success', __('team-invitations.You are now part of the :team team!', ['team' => $teamName]));
                
                // Redirect to dashboard after processing invitation
                return redirect()->route('root.dashboard.hierarchy');
            } else {
                Log::warning('Email validation failed', [
                    'invitation_email' => $invitation->email,
                    'session_email' => $invitationEmail,
                    'user_email' => $user->email,
                ]);
                
                // If there's any inconsistency, clear the session
                session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
            }
        }
        
        return $next($request);
    }
}
