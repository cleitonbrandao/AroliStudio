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
        Log::info('ProcessPendingTeamInvitation executado', [
            'is_authenticated' => Auth::check(),
            'has_session_invitation' => session()->has('team_invitation_id'),
            'session_invitation_id' => session('team_invitation_id'),
            'session_invitation_email' => session('team_invitation_email'),
            'user_email' => Auth::user()?->email,
            'url' => $request->url(),
        ]);
        
        // Se o usuário acabou de logar e há um convite pendente na sessão
        if (Auth::check() && session()->has('team_invitation_id')) {
            $invitationId = session('team_invitation_id');
            $invitationEmail = session('team_invitation_email');
            $teamName = session('team_invitation_team');
            
            $user = Auth::user();
            
            Log::info('Processando convite pendente', [
                'invitation_id' => $invitationId,
                'invitation_email' => $invitationEmail,
                'user_email' => $user->email,
            ]);
            
            // Busca o convite
            $invitation = TeamInvitation::find($invitationId);
            
            if (!$invitation) {
                Log::warning('Convite não encontrado', ['invitation_id' => $invitationId]);
                session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
                return $next($request);
            }
            
            // Valida se o convite existe e o email corresponde
            if ($invitation->email === $invitationEmail && $invitation->email === $user->email) {
                Log::info('Emails validados, adicionando usuário ao time');
                
                // Adiciona o membro ao time
                app(AddsTeamMembers::class)->add(
                    $invitation->team->owner,
                    $invitation->team,
                    $invitation->email,
                    $invitation->role
                );
                
                Log::info('Usuário adicionado ao time com sucesso');
                
                // Deleta o convite
                $invitation->delete();
                
                // Remove da sessão (incluindo url.intended para evitar redirect para URL com signature expirada)
                session()->forget([
                    'team_invitation_id', 
                    'team_invitation_email', 
                    'team_invitation_team',
                    'url.intended' // Remove intended URL que pode ter signature expirada
                ]);
                
                // Adiciona mensagem de sucesso
                session()->flash('success', __('team-invitations.You are now part of the :team team!', ['team' => $teamName]));
                
                // Redireciona para o dashboard após processar o convite
                return redirect()->route('root.dashboard.hierarchy');
            } else {
                Log::warning('Validação de emails falhou', [
                    'invitation_email' => $invitation->email,
                    'session_email' => $invitationEmail,
                    'user_email' => $user->email,
                ]);
                
                // Se houver alguma inconsistência, limpa a sessão
                session()->forget(['team_invitation_id', 'team_invitation_email', 'team_invitation_team', 'url.intended']);
            }
        }
        
        return $next($request);
    }
}
