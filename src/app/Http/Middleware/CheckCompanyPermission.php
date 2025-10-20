<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Services\FranchiseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyPermission
{
    public function __construct(
        private FranchiseService $franchiseService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action = 'read'): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Obter empresa do parâmetro da rota ou do contexto atual
        $company = $request->route('company') ?? $user->currentTeam;
        
        if (!$company instanceof Company) {
            return redirect()->route('companies.index')
                ->with('error', 'Empresa não encontrada.');
        }

        // Verificar se usuário tem permissão para a ação
        if (!$this->franchiseService->canUserAccessCompany($user, $company, $action)) {
            $role = $user->getRoleInCompany($company);
            
            return redirect()->back()
                ->with('error', "Você não tem permissão para {$action} nesta empresa. Seu role: {$role}");
        }

        return $next($request);
    }
}
