<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionLimits
{
    public function __construct(
        private SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $action): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        switch ($action) {
            case 'create_company':
                $limits = $this->subscriptionService->canUserCreateCompany($user);
                if (!$limits['can_create']) {
                    return redirect()->route('subscription.upgrade')
                        ->with('error', "Você atingiu o limite de {$limits['limit']} empresa(s) do seu plano atual. Faça upgrade para criar mais empresas.");
                }
                break;

            case 'add_user':
                $company = $request->route('company') ?? $user->currentTeam;
                if ($company) {
                    $limits = $this->subscriptionService->canCompanyAddUser($company);
                    if (!$limits['can_add']) {
                        return redirect()->back()
                            ->with('error', "Você atingiu o limite de {$limits['limit']} usuário(s) do seu plano atual. Faça upgrade para adicionar mais usuários.");
                    }
                }
                break;
        }

        return $next($request);
    }
}
