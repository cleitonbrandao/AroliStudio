<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Controller para gerenciar mudanças de locale (idioma/região) do Team.
 *
 * Quando o gerente/owner muda o locale do team:
 * 1. Verifica permissão (apenas manager/owner)
 * 2. Atualiza team->locale no banco
 * 3. SetLocale middleware lê na próxima request
 * 4. App::setLocale() é chamado
 * 5. Moeda é mapeada automaticamente via config/currency.php
 * 6. MoneyWrapper formata valores com base no locale
 */
class LocaleController extends Controller
{
    /**
     * Locales válidos suportados pela aplicação.
     */
    private const VALID_LOCALES = ['pt_BR', 'en', 'es', 'de'];

    /**
     * Muda o locale do Team.
     * Apenas Owner e usuários com role 'manager' podem alterar.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(Request $request)
    {
        // Guard: Verifica se usuário está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', __('app.authentication_required'));
        }

        $user = Auth::user();
        $team = $user->currentTeam;

        // Verifica se usuário tem team
        if (!$team) {
            return redirect()->back()->with('error', __('app.no_team_selected'));
        }

        // Verifica permissão: Owner ou Manager
        if (!$this->canChangeLocale($user, $team)) {
            return redirect()->back()->with('error', __('app.no_permission_change_locale'));
        }

        $locale = $request->input('locale', config('app.locale'));

        // Log
        Log::info('LocaleController: Mudando locale do team', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'from' => $team->locale,
            'to' => $locale,
        ]);

        // Valida se o locale é suportado
        if (!in_array($locale, self::VALID_LOCALES)) {
            Log::warning('LocaleController: Locale inválido', ['locale' => $locale]);
            return redirect()->back()->with('error', __('app.invalid_locale'));
        }

        // Atualiza o locale do team
        $team->update(['locale' => $locale]);

        // Log de confirmação
        Log::info('LocaleController: Locale do team atualizado', [
            'team_id' => $team->id,
            'locale' => $locale,
            'currency' => config("currency.locale_currency_map.{$locale}"),
        ]);

        $currency = config("currency.locale_currency_map.{$locale}", config('currency.default'));

        return redirect()->back()->with([
            'locale_changed' => __('app.locale_changed'),
            'locale' => $locale,
            'currency' => $currency,
        ]);
    }

        /**
     * Check if user can change locale
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Team  $team
     * @return bool
     */
    private function canChangeLocale($user, $team): bool
    {
        // Use the existing method from User model
        return $user->canManageTeam($team);
    }

    /**
     * Retorna o locale atual do team e se o usuário pode alterá-lo.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        // Guard: Verifica se usuário está autenticado
        if (!Auth::check()) {
            return response()->json([
                'locale' => config('app.locale'),
                'currency' => config('currency.default'),
                'available_locales' => self::VALID_LOCALES,
                'can_change' => false,
                'authenticated' => false,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $team = $user->currentTeam;

        return response()
            ->json(
                [
                'locale' => $team ? $team->locale : config('app.locale'),
                'currency' => $team ? config("currency.locale_currency_map.{$team->locale}", config('currency.default')) : config('currency.default'),
                'available_locales' => self::VALID_LOCALES,
                'can_change' => $team ? $this->canChangeLocale($user, $team) : false,
            ],
            Response::HTTP_OK
        );
    }
}
