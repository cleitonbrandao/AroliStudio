<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Controller para gerenciar mudanças de locale (idioma/região) do usuário.
 * 
 * Quando o usuário muda o locale:
 * 1. Salva na sessão
 * 2. SetLocale middleware detecta na próxima request
 * 3. App::setLocale() é chamado
 * 4. Moeda é mapeada automaticamente via config/currency.php
 * 5. MoneyWrapper formata valores com base no locale
 */
class LocaleController extends Controller
{
    /**
     * Locales válidos suportados pela aplicação.
     */
    private const VALID_LOCALES = ['pt_BR', 'en', 'es', 'de'];

    /**
     * Muda o locale do usuário.
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(Request $request)
    {
        $locale = $request->input('locale', config('app.locale'));
        
        // Log para debug
        Log::info('LocaleController: Mudando locale', [
            'from' => app()->getLocale(),
            'to' => $locale,
            'session_id' => session()->getId(),
        ]);
        
        // Valida se o locale é suportado
        if (!in_array($locale, self::VALID_LOCALES)) {
            Log::warning('LocaleController: Locale inválido', ['locale' => $locale]);
            return redirect()->back()->with('error', __('app.invalid_locale'));
        }
        
        // Salva o locale na sessão
        Session::put('locale', $locale);
        Session::save(); // Força salvar imediatamente
        
        // Log de confirmação
        Log::info('LocaleController: Locale salvo na sessão', [
            'locale' => Session::get('locale'),
            'currency' => config("currency.locale_currency_map.{$locale}", config('currency.default')),
        ]);
        
        // Obtém a moeda correspondente ao locale
        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));
        
        return redirect()->back()->with([
            'success' => __('app.locale_changed'),
            'locale' => $locale,
            'currency' => $currency,
        ]);
    }

    /**
     * Retorna o locale atual.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        return response()->json([
            'locale' => app()->getLocale(),
            'currency' => Session::get('currency', config('currency.default')),
            'available_locales' => self::VALID_LOCALES,
        ]);
    }
}
