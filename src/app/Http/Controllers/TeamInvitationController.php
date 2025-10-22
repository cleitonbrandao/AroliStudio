<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
use Laravel\Jetstream\TeamInvitation;

class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Jetstream\TeamInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, TeamInvitation $invitation)
    {
        // Debug logging
        Log::info('TeamInvitationController::accept called', [
            'invitation_id' => $invitation->id,
            'invitation_email' => $invitation->email,
            'is_authenticated' => Auth::check(),
            'user_email' => Auth::user()?->email,
        ]);
        
        // If user is NOT authenticated, show invitation page
        if (!Auth::check()) {
            Log::info('User not authenticated, showing invitation page');
            
            // Detect browser's preferred language
            $this->setLocaleFromBrowser($request);
            
            // Store invitation information in session to process after login/registration
            // Use regenerate(true) to maintain data during session regeneration on login
            $request->session()->put([
                'team_invitation_id' => $invitation->id,
                'team_invitation_email' => $invitation->email,
                'team_invitation_team' => $invitation->team->name,
            ]);
            
            // FORCE session save before showing view
            $request->session()->save();
            
            // Mark that there's a pending invitation (this data will be preserved even with regenerate)
            $request->session()->flash('_team_invitation_pending', true);
            
            Log::info('Session saved with pending invitation', [
                'session_id' => $request->session()->getId(),
                'session_has_invitation' => $request->session()->has('team_invitation_id'),
                'invitation_id' => $request->session()->get('team_invitation_id'),
                'invitation_email' => $request->session()->get('team_invitation_email'),
            ]);
            
            // Show page with invitation details
            return view('team-invitations.show', [
                'invitation' => $invitation,
                'teamName' => $invitation->team->name,
                'invitationEmail' => $invitation->email,
                'roleName' => $this->getRoleName($invitation->role),
            ]);
        }

        // Check if invitation is for the authenticated user's email
        if ($invitation->email !== Auth::user()->email) {
            return redirect()->route('root.dashboard.hierarchy')->with([
                'error' => __('team-invitations.This invitation was sent to :email, but you are logged in as :current_email.', [
                    'email' => $invitation->email,
                    'current_email' => Auth::user()->email,
                ]),
            ]);
        }

        // Adiciona o membro ao time
        app(AddsTeamMembers::class)->add(
            $invitation->team->owner,
            $invitation->team,
            $invitation->email,
            $invitation->role
        );

        // Deleta o convite
        $invitation->delete();

        return redirect()->route('root.dashboard.hierarchy')->with([
            'success' => __('team-invitations.You are now part of the :team team!', ['team' => $invitation->team->name]),
        ]);
    }

    /**
     * Traduz o nome da role para exibição.
     */
    private function getRoleName(?string $role): string
    {
        return __('team-invitations.roles.' . ($role ?? 'member'));
    }

    /**
     * Define o locale baseado nas preferências do navegador.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    private function setLocaleFromBrowser(Request $request): void
    {
        // Pega o header Accept-Language do navegador
        $acceptLanguage = $request->header('Accept-Language');
        
        if (!$acceptLanguage) {
            return;
        }

        // Parse do Accept-Language header
        // Formato: "pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7"
        $languages = $this->parseAcceptLanguage($acceptLanguage);
        
        // Idiomas suportados pela aplicação
        $supportedLocales = ['pt_BR', 'en', 'es'];
        
        // Mapeamento de códigos do navegador para locales da aplicação
        $localeMap = [
            'pt-br' => 'pt_BR',
            'pt' => 'pt_BR',
            'en-us' => 'en',
            'en-gb' => 'en',
            'en' => 'en',
            'es-es' => 'es',
            'es' => 'es',
        ];
        
        // Encontra o primeiro idioma suportado
        foreach ($languages as $lang) {
            $langLower = strtolower($lang);
            
            if (isset($localeMap[$langLower])) {
                $locale = $localeMap[$langLower];
                
                if (in_array($locale, $supportedLocales)) {
                    App::setLocale($locale);
                    Log::info('Locale definido baseado no navegador', [
                        'accept_language' => $acceptLanguage,
                        'detected_language' => $lang,
                        'locale_set' => $locale,
                    ]);
                    return;
                }
            }
        }
        
        Log::info('Nenhum locale compatível encontrado, usando fallback', [
            'accept_language' => $acceptLanguage,
            'fallback_locale' => config('app.fallback_locale'),
        ]);
    }

    /**
     * Faz o parse do header Accept-Language.
     * 
     * @param  string  $acceptLanguage
     * @return array
     */
    private function parseAcceptLanguage(string $acceptLanguage): array
    {
        // Remove espaços e divide por vírgula
        $languages = explode(',', $acceptLanguage);
        $parsed = [];
        
        foreach ($languages as $language) {
            // Remove o quality factor (q=0.9)
            $parts = explode(';', trim($language));
            $langCode = trim($parts[0]);
            
            // Pega o quality factor se existir
            $quality = 1.0;
            if (isset($parts[1]) && preg_match('/q=([\d.]+)/', $parts[1], $matches)) {
                $quality = (float) $matches[1];
            }
            
            $parsed[] = [
                'code' => $langCode,
                'quality' => $quality,
            ];
        }
        
        // Ordena por quality (maior primeiro)
        usort($parsed, function($a, $b) {
            return $b['quality'] <=> $a['quality'];
        });
        
        // Retorna apenas os códigos de idioma
        return array_column($parsed, 'code');
    }
}
