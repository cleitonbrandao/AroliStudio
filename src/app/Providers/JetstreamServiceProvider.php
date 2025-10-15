<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        // 👑 SUPER_ADMIN - Dono do sistema (acesso total a tudo)
        Jetstream::role('super_admin', 'Super Administrador', [
            'system_manage',
            'company_create',
            'company_delete',
            'user_manage',
            'billing_manage',
            'create',
            'read',
            'update',
            'delete',
        ])->description('Acesso total ao sistema. Pode gerenciar todas as empresas e usuários.');

        // 🏢 CEO - Dono da matriz/holding (controle total da empresa)
        Jetstream::role('ceo', 'CEO/Diretor', [
            'company_manage',
            'franchise_create',
            'franchise_delete',
            'user_manage',
            'billing_view',
            'reports_all',
            'create',
            'read',
            'update',
            'delete',
        ])->description('CEO ou Diretor da empresa matriz. Controle total sobre a organização.');

        // 🏪 FRANCHISE_OWNER - Dono da filial (controle da filial)
        Jetstream::role('franchise_owner', 'Franqueado', [
            'franchise_manage',
            'user_manage',
            'reports_franchise',
            'create',
            'read',
            'update',
        ])->description('Franqueado ou dono de filial. Gerencia sua unidade.');

        // ⚙️ REGIONAL_MANAGER - Gerente regional (múltiplas filiais)
        Jetstream::role('regional_manager', 'Gerente Regional', [
            'franchise_supervise',
            'user_supervise',
            'reports_regional',
            'create',
            'read',
            'update',
        ])->description('Gerente regional. Supervisiona múltiplas filiais.');

        // 👨‍💼 MANAGER - Gerente local (uma filial)
        Jetstream::role('manager', 'Gerente', [
            'team_manage',
            'user_supervise',
            'reports_local',
            'create',
            'read',
            'update',
        ])->description('Gerente local. Gerencia equipe de uma filial.');

        // 👥 SUPERVISOR - Supervisor (equipe)
        Jetstream::role('supervisor', 'Supervisor', [
            'team_supervise',
            'reports_team',
            'create',
            'read',
            'update',
        ])->description('Supervisor. Supervisiona equipe específica.');

        // 👤 EMPLOYEE - Funcionário (acesso básico)
        Jetstream::role('employee', 'Funcionário', [
            'read',
        ])->description('Funcionário. Acesso básico de leitura.');

        // 🔍 AUDITOR - Auditor (apenas leitura)
        Jetstream::role('auditor', 'Auditor', [
            'read',
            'reports_view',
        ])->description('Auditor. Acesso apenas de leitura para auditoria.');
    }
}
