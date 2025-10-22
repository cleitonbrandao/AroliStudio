<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
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
        
        Log::info('ProcessPendingTeamInvitationAfterLogin executado', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'has_session_invitation' => session()->has('team_invitation_id'),
            'session_invitation_id' => session('team_invitation_id'),
            'session_invitation_email' => session('team_invitation_email'),
        ]);
        
        // Verifica se há um convite pendente na sessão
        if (!session()->has('team_invitation_id')) {
            Log::info('Nenhum convite pendente na sessão');
            return;
        }
        
        $invitationId = session('team_invitation_id');
        $invitationEmail = session('team_invitation_email');
        $teamName = session('team_invitation_team');
        
        Log::info('Convite pendente encontrado na sessão', [
            'invitation_id' => $invitationId,
            'invitation_email' => $invitationEmail,
            'team_name' => $teamName,
        ]);
        
        // Busca o convite
        $invitation = TeamInvitation::find($invitationId);
        
        if (!$invitation) {
            Log::warning('Convite não encontrado no banco de dados', ['invitation_id' => $invitationId]);
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            return;
        }
        
        // Valida se o email do convite corresponde ao email do usuário que logou
        if ($invitation->email !== $user->email) {
            Log::warning('Email do convite não corresponde ao email do usuário', [
                'invitation_email' => $invitation->email,
                'user_email' => $user->email,
            ]);
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
            return;
        }
        
        try {
            Log::info('Adicionando usuário ao time', [
                'team_id' => $invitation->team_id,
                'user_email' => $user->email,
                'role' => $invitation->role,
            ]);
            
            // Adiciona o usuário ao time
            app(AddsTeamMembers::class)->add(
                $invitation->team->owner,
                $invitation->team,
                $invitation->email,
                $invitation->role
            );
            
            Log::info('Usuário adicionado ao time com sucesso');
            
            // Deleta o convite
            $invitation->delete();
            
            Log::info('Convite deletado com sucesso');
            
            // Remove da sessão
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
            
            // Adiciona mensagem de sucesso
            session()->flash('success', __('team-invitations.You are now part of the :team team!', ['team' => $teamName]));
            
            Log::info('Processo de convite finalizado com sucesso');
            
        } catch (\Exception $e) {
            Log::error('Erro ao processar convite pendente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team']);
        }
    }
}
