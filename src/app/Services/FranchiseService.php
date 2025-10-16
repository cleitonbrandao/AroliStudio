<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FranchiseService
{
    /**
     * Criar filial de uma empresa matriz
     */
    public function createFranchise(Company $parentCompany, User $franchiseOwner, array $franchiseData): Company
    {
        return DB::transaction(function () use ($parentCompany, $franchiseOwner, $franchiseData) {
            // Criar a filial
            $franchise = Company::create([
                'name' => $franchiseData['name'],
                'user_id' => $franchiseOwner->id,
                'personal_team' => false,
                'max_users' => $franchiseData['max_users'] ?? 10,
                'current_users' => 1,
                'plan_type' => 'franchise',
                'is_active' => true,
            ]);

            // Adicionar o dono da filial como franchise_owner
            $franchise->addUserWithCount($franchiseOwner, 'franchise_owner');

            // Adicionar representantes da matriz como regional_manager na filial
            if (isset($franchiseData['matrix_managers'])) {
                foreach ($franchiseData['matrix_managers'] as $managerId) {
                    $manager = User::find($managerId);
                    if ($manager && in_array($parentCompany->getUserRole($manager), ['ceo', 'regional_manager'])) {
                        $franchise->addUserWithCount($manager, 'regional_manager');
                    }
                }
            }

            // Adicionar o dono da filial como auditor na matriz (para relatórios)
            if (!$parentCompany->users()->where('user_id', $franchiseOwner->id)->exists()) {
                $parentCompany->addUserWithCount($franchiseOwner, 'auditor');
            }

            return $franchise;
        });
    }

    /**
     * Obter hierarquia de empresas (matriz -> filiais)
     */
    public function getCompanyHierarchy(Company $company): array
    {
        $hierarchy = [
            'company' => $company,
            'franchises' => [],
            'parent' => null,
        ];

        // Buscar filiais (empresas onde este usuário é owner mas não é a empresa principal)
        $franchises = Company::where('user_id', $company->user_id)
            ->where('id', '!=', $company->id)
            ->where('plan_type', 'franchise')
            ->get();

        $hierarchy['franchises'] = $franchises;

        return $hierarchy;
    }

    /**
     * Obter todas as empresas onde o usuário tem acesso (com roles)
     */
    public function getUserCompaniesWithRoles(User $user): array
    {
        $companies = [];

        // Empresas onde é owner
        $ownedCompanies = $user->ownedCompanies()->get();
        foreach ($ownedCompanies as $company) {
            $companies[] = [
                'company' => $company,
                'role' => 'owner',
                'permissions' => ['create', 'read', 'update', 'delete'],
            ];
        }

        // Empresas onde é admin
        $adminCompanies = $user->adminCompanies()->get();
        foreach ($adminCompanies as $company) {
            $companies[] = [
                'company' => $company,
                'role' => 'admin',
                'permissions' => ['create', 'read', 'update'],
            ];
        }

        // Empresas onde é member
        $memberCompanies = $user->memberCompanies()->get();
        foreach ($memberCompanies as $company) {
            $companies[] = [
                'company' => $company,
                'role' => 'member',
                'permissions' => ['read'],
            ];
        }

        return $companies;
    }

    /**
     * Verificar se usuário pode acessar dados de uma empresa
     */
    public function canUserAccessCompany(User $user, Company $company, string $action = 'read'): bool
    {
        $role = $user->getRoleInCompany($company);
        
        if (!$role) {
            return false;
        }

        $permissions = $this->getPermissionsByRole($role);
        
        return in_array($action, $permissions);
    }

    /**
     * Obter permissões por role
     */
    private function getPermissionsByRole(string $role): array
    {
        return match ($role) {
            'owner' => ['create', 'read', 'update', 'delete'],
            'admin' => ['create', 'read', 'update'],
            'member' => ['read'],
            default => [],
        };
    }

    /**
     * Exemplo prático: Cenário de franquia
     */
    public function getFranchiseExample(): array
    {
        return [
            'scenario' => 'Franquia de Restaurantes - Nova Hierarquia',
            'structure' => [
                'matriz' => [
                    'name' => 'Restaurante Matriz Ltda',
                    'owner' => 'João Silva (CEO)',
                    'role' => 'ceo',
                    'permissions' => ['company_manage', 'franchise_create', 'user_manage', 'reports_all'],
                ],
                'filiais' => [
                    [
                        'name' => 'Restaurante Filial Centro',
                        'owner' => 'Maria Santos (Franqueada)',
                        'role_in_franchise' => 'franchise_owner',
                        'role_in_matrix' => 'auditor',
                        'permissions_franchise' => ['franchise_manage', 'user_manage', 'reports_franchise'],
                        'permissions_matrix' => ['read', 'reports_view'],
                    ],
                    [
                        'name' => 'Restaurante Filial Shopping',
                        'owner' => 'Carlos Oliveira (Franqueado)',
                        'role_in_franchise' => 'franchise_owner',
                        'role_in_matrix' => 'auditor',
                        'permissions_franchise' => ['franchise_manage', 'user_manage', 'reports_franchise'],
                        'permissions_matrix' => ['read', 'reports_view'],
                    ],
                ],
                'gerentes' => [
                    [
                        'name' => 'Ana Costa (Gerente Regional)',
                        'role_in_matrix' => 'regional_manager',
                        'role_in_franchises' => 'regional_manager',
                        'permissions' => ['franchise_supervise', 'user_supervise', 'reports_regional'],
                    ],
                ],
                'funcionarios' => [
                    [
                        'name' => 'Pedro Silva (Gerente Local)',
                        'role_in_franchise' => 'manager',
                        'permissions' => ['team_manage', 'user_supervise', 'reports_local'],
                    ],
                    [
                        'name' => 'Sofia Costa (Supervisora)',
                        'role_in_franchise' => 'supervisor',
                        'permissions' => ['team_supervise', 'reports_team'],
                    ],
                    [
                        'name' => 'Carlos Santos (Funcionário)',
                        'role_in_franchise' => 'employee',
                        'permissions' => ['read'],
                    ],
                ],
            ],
        ];
    }
}
