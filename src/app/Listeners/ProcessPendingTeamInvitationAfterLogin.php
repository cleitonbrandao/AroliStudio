<?php

namespace App\Listeners;

use App\Services\TeamInvitationService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class ProcessPendingTeamInvitationAfterLogin
{
    /**
     * Create a new listener instance.
     */
    public function __construct(
        protected TeamInvitationService $invitationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        
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
        
        Log::info('Pending invitation found in session', [
            'invitation_id' => $invitationId,
            'invitation_email' => $invitationEmail,
        ]);
        
        // Process the invitation using the service
        $result = $this->invitationService->processPendingInvitation(
            $invitationId,
            $invitationEmail,
            $user
        );
        
        // Clear session data
        $this->invitationService->clearInvitationSession();
        
        // Flash appropriate message
        if ($result['success']) {
            session()->flash('success', $result['message']);
            Log::info('Invitation process completed successfully');
        } else {
            session()->flash('error', $result['message']);
            Log::warning('Invitation process failed', ['reason' => $result['message']]);
        }
    }
}
