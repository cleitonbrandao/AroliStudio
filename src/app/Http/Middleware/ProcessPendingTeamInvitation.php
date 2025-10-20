<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // Se o usuário acabou de logar e há um convite pendente na sessão
        if (Auth::check() && session()->has('team_invitation_id')) {
            $invitationId = session('team_invitation_id');
            $teamName = session('team_invitation_team');
            
            // Remove da sessão
            session()->forget(['team_invitation_id', 'team_invitation_team']);
            
            // Busca o convite
            $invitation = TeamInvitation::find($invitationId);
            
            if ($invitation && $invitation->email === Auth::user()->email) {
                // Adiciona o membro ao time
                app(AddsTeamMembers::class)->add(
                    $invitation->team->owner,
                    $invitation->team,
                    $invitation->email,
                    $invitation->role
                );
                
                // Deleta o convite
                $invitation->delete();
                
                // Adiciona mensagem de sucesso
                session()->flash('success', __('team-invitations.You are now part of the :team team!', ['team' => $teamName]));
            }
        }
        
        return $next($request);
    }
}
