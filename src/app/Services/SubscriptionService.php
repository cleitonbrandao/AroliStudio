<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscription;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Criar assinatura para usuário
     */
    public function createUserSubscription(User $user, array $subscriptionData): Subscription
    {
        return DB::transaction(function () use ($user, $subscriptionData) {
            $subscription = $user->subscriptions()->create([
                'name' => $subscriptionData['name'],
                'stripe_id' => $subscriptionData['stripe_id'],
                'stripe_status' => $subscriptionData['stripe_status'],
                'stripe_price' => $subscriptionData['stripe_price'] ?? null,
                'quantity' => $subscriptionData['quantity'] ?? 1,
                'trial_ends_at' => $subscriptionData['trial_ends_at'] ?? null,
                'ends_at' => $subscriptionData['ends_at'] ?? null,
            ]);

            return $subscription;
        });
    }

    /**
     * Criar assinatura para empresa
     */
    public function createCompanySubscription(Company $company, Subscription $subscription, array $planData): CompanySubscription
    {
        return DB::transaction(function () use ($company, $subscription, $planData) {
            $companySubscription = $company->subscriptions()->create([
                'subscription_id' => $subscription->id,
                'plan_type' => $planData['plan_type'],
                'max_users' => $planData['max_users'],
                'max_companies' => $planData['max_companies'],
                'is_active' => true,
                'starts_at' => now(),
                'ends_at' => $planData['ends_at'] ?? null,
            ]);

            // Atualizar limites da empresa
            $company->update([
                'max_users' => $planData['max_users'],
                'plan_type' => $planData['plan_type'],
                'is_active' => true,
            ]);

            return $companySubscription;
        });
    }

    /**
     * Verificar se usuário pode criar empresa
     */
    public function canUserCreateCompany(User $user): array
    {
        $currentCompanies = $user->ownedTeams()->count();
        $subscription = $user->activeSubscription();
        
        if (!$subscription) {
            return [
                'can_create' => $currentCompanies < 1,
                'current' => $currentCompanies,
                'limit' => 1,
                'plan' => 'free',
            ];
        }

        $companySubscription = $subscription->companySubscriptions()->first();
        $limit = $companySubscription ? $companySubscription->max_companies : 1;

        return [
            'can_create' => $currentCompanies < $limit,
            'current' => $currentCompanies,
            'limit' => $limit,
            'plan' => $companySubscription->plan_type ?? 'free',
        ];
    }

    /**
     * Verificar se empresa pode adicionar usuário
     */
    public function canCompanyAddUser(Company $company): array
    {
        $subscription = $company->activeSubscription();
        
        if (!$subscription) {
            return [
                'can_add' => $company->current_users < $company->max_users,
                'current' => $company->current_users,
                'limit' => $company->max_users,
                'plan' => 'free',
            ];
        }

        return [
            'can_add' => $company->current_users < $subscription->max_users,
            'current' => $company->current_users,
            'limit' => $subscription->max_users,
            'plan' => $subscription->plan_type,
        ];
    }

    /**
     * Obter planos disponíveis
     */
    public function getAvailablePlans(): array
    {
        return [
            'free' => [
                'name' => 'Plano Gratuito',
                'max_users' => 5,
                'max_companies' => 1,
                'price' => 0,
                'features' => [
                    'Até 5 usuários por empresa',
                    '1 empresa',
                    'Suporte por email',
                ],
            ],
            'basic' => [
                'name' => 'Plano Básico',
                'max_users' => 25,
                'max_companies' => 3,
                'price' => 29.90,
                'features' => [
                    'Até 25 usuários por empresa',
                    'Até 3 empresas',
                    'Suporte prioritário',
                    'Relatórios avançados',
                ],
            ],
            'premium' => [
                'name' => 'Plano Premium',
                'max_users' => 100,
                'max_companies' => 10,
                'price' => 79.90,
                'features' => [
                    'Até 100 usuários por empresa',
                    'Até 10 empresas',
                    'Suporte 24/7',
                    'API completa',
                    'Integrações avançadas',
                ],
            ],
            'enterprise' => [
                'name' => 'Plano Enterprise',
                'max_users' => -1, // Ilimitado
                'max_companies' => -1, // Ilimitado
                'price' => 199.90,
                'features' => [
                    'Usuários ilimitados',
                    'Empresas ilimitadas',
                    'Suporte dedicado',
                    'Customizações',
                    'SLA garantido',
                ],
            ],
        ];
    }

    /**
     * Cancelar assinatura
     */
    public function cancelSubscription(Subscription $subscription): bool
    {
        return DB::transaction(function () use ($subscription) {
            // Cancelar no Stripe (implementar quando integrar)
            // $subscription->cancel();
            
            $subscription->update([
                'stripe_status' => 'cancelled',
                'ends_at' => now()->addDays(30), // Grace period
            ]);

            // Desativar assinaturas das empresas
            $subscription->companySubscriptions()->update([
                'is_active' => false,
                'ends_at' => now()->addDays(30),
            ]);

            return true;
        });
    }
}
