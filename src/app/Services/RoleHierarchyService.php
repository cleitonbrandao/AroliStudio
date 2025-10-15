<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;

class RoleHierarchyService
{
    /**
     * Hierarquia de roles (do maior para o menor)
     */
    private array $hierarchy = [
        'super_admin' => 100,
        'ceo' => 90,
        'franchise_owner' => 80,
        'regional_manager' => 70,
        'manager' => 60,
        'supervisor' => 50,
        'employee' => 30,
        'auditor' => 20,
    ];

    /**
     * Obter nível hierárquico de um role
     */
    public function getRoleLevel(string $role): int
    {
        return $this->hierarchy[$role] ?? 0;
    }

    /**
     * Verificar se um role é superior a outro
     */
    public function isRoleSuperior(string $role1, string $role2): bool
    {
        return $this->getRoleLevel($role1) > $this->getRoleLevel($role2);
    }

    /**
     * Verificar se usuário pode gerenciar outro usuário
     */
    public function canUserManageUser(User $manager, User $target, Company $company): bool
    {
        $managerRole = $manager->getRoleInCompany($company);
        $targetRole = $target->getRoleInCompany($company);

        if (!$managerRole || !$targetRole) {
            return false;
        }

        // Super admin pode gerenciar qualquer um
        if ($managerRole === 'super_admin') {
            return true;
        }

        // CEO pode gerenciar qualquer um exceto super admin
        if ($managerRole === 'ceo' && $targetRole !== 'super_admin') {
            return true;
        }

        // Outros roles seguem hierarquia
        return $this->isRoleSuperior($managerRole, $targetRole);
    }

    /**
     * Verificar se usuário pode realizar ação em empresa
     */
    public function canUserPerformAction(User $user, Company $company, string $action): bool
    {
        $role = $user->getRoleInCompany($company);
        
        if (!$role) {
            return false;
        }

        return $this->roleCanPerformAction($role, $action);
    }

    /**
     * Verificar se role pode realizar ação
     */
    public function roleCanPerformAction(string $role, string $action): bool
    {
        $permissions = $this->getRolePermissions($role);
        return in_array($action, $permissions);
    }

    /**
     * Obter permissões de um role
     */
    public function getRolePermissions(string $role): array
    {
        return match ($role) {
            'super_admin' => [
                'system_manage', 'company_create', 'company_delete', 'user_manage',
                'billing_manage', 'create', 'read', 'update', 'delete'
            ],
            'ceo' => [
                'company_manage', 'franchise_create', 'franchise_delete', 'user_manage',
                'billing_view', 'reports_all', 'create', 'read', 'update', 'delete'
            ],
            'franchise_owner' => [
                'franchise_manage', 'user_manage', 'reports_franchise',
                'create', 'read', 'update'
            ],
            'regional_manager' => [
                'franchise_supervise', 'user_supervise', 'reports_regional',
                'create', 'read', 'update'
            ],
            'manager' => [
                'team_manage', 'user_supervise', 'reports_local',
                'create', 'read', 'update'
            ],
            'supervisor' => [
                'team_supervise', 'reports_team', 'create', 'read', 'update'
            ],
            'employee' => ['read'],
            'auditor' => ['read', 'reports_view'],
            default => [],
        };
    }

    /**
     * Obter roles que um usuário pode atribuir
     */
    public function getAssignableRoles(User $user, Company $company): array
    {
        $userRole = $user->getRoleInCompany($company);
        $userLevel = $this->getRoleLevel($userRole);

        $assignableRoles = [];

        foreach ($this->hierarchy as $role => $level) {
            if ($level < $userLevel) {
                $assignableRoles[] = $role;
            }
        }

        return $assignableRoles;
    }

    /**
     * Verificar se usuário pode criar filial
     */
    public function canUserCreateFranchise(User $user, Company $parentCompany): bool
    {
        $role = $user->getRoleInCompany($parentCompany);
        
        return in_array($role, ['super_admin', 'ceo']);
    }

    /**
     * Verificar se usuário pode deletar empresa
     */
    public function canUserDeleteCompany(User $user, Company $company): bool
    {
        $role = $user->getRoleInCompany($company);
        
        return in_array($role, ['super_admin', 'ceo']);
    }

    /**
     * Obter hierarquia visual
     */
    public function getHierarchyVisual(): array
    {
        return [
            'super_admin' => [
                'name' => 'Super Administrador',
                'icon' => '👑',
                'level' => 100,
                'description' => 'Dono do sistema - Acesso total',
                'color' => 'purple',
            ],
            'ceo' => [
                'name' => 'CEO/Diretor',
                'icon' => '🏢',
                'level' => 90,
                'description' => 'Dono da matriz - Controle total',
                'color' => 'red',
            ],
            'franchise_owner' => [
                'name' => 'Franqueado',
                'icon' => '🏪',
                'level' => 80,
                'description' => 'Dono da filial - Gerencia unidade',
                'color' => 'blue',
            ],
            'regional_manager' => [
                'name' => 'Gerente Regional',
                'icon' => '⚙️',
                'level' => 70,
                'description' => 'Supervisiona múltiplas filiais',
                'color' => 'green',
            ],
            'manager' => [
                'name' => 'Gerente',
                'icon' => '👨‍💼',
                'level' => 60,
                'description' => 'Gerencia equipe local',
                'color' => 'yellow',
            ],
            'supervisor' => [
                'name' => 'Supervisor',
                'icon' => '👥',
                'level' => 50,
                'description' => 'Supervisiona equipe',
                'color' => 'orange',
            ],
            'employee' => [
                'name' => 'Funcionário',
                'icon' => '👤',
                'level' => 30,
                'description' => 'Acesso básico',
                'color' => 'gray',
            ],
            'auditor' => [
                'name' => 'Auditor',
                'icon' => '🔍',
                'level' => 20,
                'description' => 'Apenas leitura',
                'color' => 'indigo',
            ],
        ];
    }
}
