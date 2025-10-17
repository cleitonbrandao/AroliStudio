<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
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
        // Obtém o locale da sessão ou usa o padrão
        $locale = Session::get('locale', config('app.locale'));
        
        // Log para debug (pode remover em produção)
        Log::debug('SetLocale middleware', [
            'session_locale' => Session::get('locale'),
            'config_locale' => config('app.locale'),
            'final_locale' => $locale,
            'route' => $request->path(),
        ]);
        
        // Define o locale da aplicação
        App::setLocale($locale);
        
        // Define a moeda baseada no locale
        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
        Session::put('currency', $currency);
        
        // Disponibiliza a moeda para todas as views
        view()->share('currentCurrency', $currency);
        view()->share('currentLocale', $locale);
        
        return $next($request);
    }
}
