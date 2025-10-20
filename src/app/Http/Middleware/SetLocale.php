<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtém o locale do currentTeam do usuário autenticado
        $locale = $this->resolveLocale($request);
        
        // Log para debug (apenas em ambiente de desenvolvimento)
        if (config('app.debug')) {
            Log::debug('SetLocale middleware', [
                'user_id' => Auth::id(),
                'team_id' => Auth::user()?->currentTeam?->id,
                'team_locale' => Auth::user()?->currentTeam?->locale,
                'final_locale' => $locale,
                'route' => $request->path(),
            ]);
        }
        
        // Define o locale da aplicação
        App::setLocale($locale);
        
        // Define a moeda baseada no locale
        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
        
        // Disponibiliza para todas as views
        view()->share('currentCurrency', $currency);
        view()->share('currentLocale', $locale);
        
        return $next($request);
    }

    /**
     * Resolve o locale a ser usado.
     * Prioridade: Team → Config Padrão
     */
    private function resolveLocale(Request $request): string
    {
        // Se usuário está autenticado e tem currentTeam
        if (Auth::check() && Auth::user()->currentTeam) {
            return Auth::user()->currentTeam->locale ?? config('app.locale');
        }
        
        // Fallback para locale padrão da aplicação
        return config('app.locale');
    }
}
