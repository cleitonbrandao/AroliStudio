<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetActiveCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Se não há empresa ativa na sessão, definir a primeira empresa do usuário
            if (!$request->session()->has('active_company_id')) {
                $firstCompany = $user->allTeams()->first();
                if ($firstCompany) {
                    $request->session()->put('active_company_id', $firstCompany->id);
                    
                    // Também definir como current team se não estiver definido
                    if (!$user->current_team_id) {
                        $user->forceFill([
                            'current_team_id' => $firstCompany->id,
                        ])->save();
                    }
                }
            }
            
            // Disponibilizar empresa ativa globalmente
            if ($request->session()->has('active_company_id')) {
                $companyId = $request->session()->get('active_company_id');
                $activeCompany = Company::find($companyId);
                
                        if ($activeCompany && $user->belongsToTeam($activeCompany)) {
                    // Compartilhar com views
                    view()->share('activeCompany', $activeCompany);
                    
                    // Disponibilizar no container
                    app()->instance('activeCompany', $activeCompany);
                } else {
                    // Se a empresa não existe ou usuário não pertence, limpar sessão
                    $request->session()->forget('active_company_id');
                }
            }
        }

        return $next($request);
    }
}
