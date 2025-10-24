<?php

namespace App\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\TeamInvitation;

class TeamInvitationService
{
    /**
     * Process a pending team invitation for the given user.
     *
     * @param  int  $invitationId
     * @param  string  $invitationEmail
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return array{success: bool, message: string, teamName: string|null}
     */
    public function processPendingInvitation(int $invitationId, string $invitationEmail, $user): array
    {
        Log::info('Processing pending invitation', [
            'invitation_id' => $invitationId,
            'invitation_email' => $invitationEmail,
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        // Find the invitation
        $invitation = TeamInvitation::find($invitationId);

        if (!$invitation) {
            Log::warning('Invitation not found', ['invitation_id' => $invitationId]);
            return [
                'success' => false,
                'message' => __('team-invitations.Invitation not found.'),
                'teamName' => null,
            ];
        }

        // Validate if invitation email matches
        if ($invitation->email !== $invitationEmail || $invitation->email !== $user->email) {
            Log::warning('Email validation failed', [
                'invitation_email' => $invitation->email,
                'session_email' => $invitationEmail,
                'user_email' => $user->email,
            ]);
            return [
                'success' => false,
                'message' => __('team-invitations.Email validation failed.'),
                'teamName' => null,
            ];
        }

        $teamName = $invitation->team->name;

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

            return [
                'success' => true,
                'message' => __('team-invitations.You are now part of the :team team!', ['team' => $teamName]),
                'teamName' => $teamName,
            ];

        } catch (ValidationException $e) {
            Log::warning('Validation error while processing invitation', [
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ]);

            return [
                'success' => false,
                'message' => __('team-invitations.Unable to process invitation due to validation error.'),
                'teamName' => $teamName,
            ];

        } catch (AuthorizationException $e) {
            Log::warning('Authorization error while processing invitation', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'team_id' => $invitation->team_id,
            ]);

            return [
                'success' => false,
                'message' => __('team-invitations.You are not authorized to join this team.'),
                'teamName' => $teamName,
            ];

        } catch (QueryException $e) {
            Log::error('Database error while processing invitation', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return [
                'success' => false,
                'message' => __('team-invitations.A database error occurred. Please try again.'),
                'teamName' => $teamName,
            ];

        } catch (\Throwable $e) {
            // Catch any other unexpected errors but let critical errors propagate
            Log::error('Unexpected error processing pending invitation', [
                'error' => $e->getMessage(),
                'type' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Re-throw critical errors
            if ($e instanceof \Error) {
                throw $e;
            }

            return [
                'success' => false,
                'message' => __('team-invitations.An unexpected error occurred.'),
                'teamName' => $teamName,
            ];
        }
    }

    /**
     * Clear invitation data from session.
     *
     * @return void
     */
    public function clearInvitationSession(): void
    {
        session()->forget([
            'team_invitation_id',
            'team_invitation_email',
            'team_invitation_team',
            'url.intended',
        ]);
    }
}
