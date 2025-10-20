<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário de teste
        $user = User::firstOrCreate(
            ['email' => 'teste@teste.com'],
            [
                'name' => 'Usuário Teste',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Criar empresas de teste
        $companies = [
            ['name' => 'Empresa Teste 1'],
            ['name' => 'Empresa Teste 2'],
            ['name' => 'Empresa Teste 3'],
        ];

        foreach ($companies as $companyData) {
            $company = Company::firstOrCreate(
                ['name' => $companyData['name']],
                array_merge($companyData, [
                    'user_id' => $user->id,
                    'personal_team' => false
                ])
            );

            // Verificar se o usuário já é membro da empresa
            if (!$user->belongsToTeam($company)) {
                $company->users()->attach($user, ['role' => 'owner']);
            }
        }

        // Definir a primeira empresa como current team
        $firstCompany = $user->allTeams()->first();
        if ($firstCompany) {
            $user->forceFill([
                'current_team_id' => $firstCompany->id,
            ])->save();
        }

        $this->command->info('Usuário de teste: teste@teste.com / password');
        $this->command->info('Empresas de teste criadas: ' . count($companies));
        $this->command->info('Current team definido: ' . ($firstCompany ? $firstCompany->name : 'Nenhuma'));
    }
}
