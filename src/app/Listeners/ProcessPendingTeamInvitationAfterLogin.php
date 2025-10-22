<?php

namespace App\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Login;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\TeamInvitation;

class ProcessPendingTeamInvitationAfterLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = request();
        
        Log::info('ProcessPendingTeamInvitationAfterLogin executed', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_session_invitation' => session()->has('team_invitation_id'),
            'session_invitation_id' => session('team_invitation_id'),
            'session_invitation_email' => session('team_invitation_email'),
        ]);
        
        // Check if there's a pending invitation in the session
        if (!session()->has('team_invitation_id')) {
            Log::info('No pending invitation found in session');
            return;
        }
        
        $invitationId = session('team_invitation_id');
        $invitationEmail = session('team_invitation_email');
        $teamName = session('team_invitation_team');
        
        Log::info('Pending invitation found in session', [
            'invitation_id' => $invitationId,
            'invitation_email' => $invitationEmail,
            'team_name' => $teamName,
        ]);
        
        // Find the invitation
        $invitation = TeamInvitation::find($invitationId);
        
        if (!$invitation) {
            Log::warning('Invitation not found in database', ['invitation_id' => $invitationId]);
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            return;
        }
        
        // Validate if the invitation email matches the logged-in user's email
        if ($invitation->email !== $user->email) {
            Log::warning('Invitation email does not match user email', [
                'invitation_email' => $invitation->email,
                'user_email' => $user->email,
            ]);
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            return;
        }
        
        try {
            Log::info('Adding user to team', [
                'team_id' => $invitation->team_id,
                'user_email' => $user->email,
                'role' => $invitation->role,
            ]);
            
            // Add user to team
            app(AddsTeamMembers::class)->add(
                $invitation->team->owner,
                $invitation->team,
                $invitation->email,
                $invitation->role
            );
            
            Log::info('User successfully added to team');
            
            // Delete the invitation
            $invitation->delete();
            
            Log::info('Invitation deleted successfully');
            
            // Remove from session
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
            
            // Add success message
            session()->flash('success', __('team-invitations.You are now part of the :team team!', ['team' => $teamName]));
            
            Log::info('Invitation process completed successfully');
            
        } catch (ValidationException $e) {
            Log::warning('Validation error while processing invitation', [
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ]);
            
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            session()->flash('error', __('team-invitations.Unable to process invitation due to validation error.'));
            
        } catch (AuthorizationException $e) {
            Log::warning('Authorization error while processing invitation', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'team_id' => $invitation->team_id,
            ]);
            
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            session()->flash('error', __('team-invitations.You are not authorized to join this team.'));
            
        } catch (QueryException $e) {
            Log::error('Database error while processing invitation', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            session()->flash('error', __('team-invitations.A database error occurred. Please try again.'));
            
        } catch (\Throwable $e) {
            // Catch any other unexpected errors but let critical errors propagate
            Log::error('Unexpected error processing pending invitation', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            
            // Re-throw critical errors
            if ($e instanceof \Error) {
                throw $e;
            }
        }
    }
}
