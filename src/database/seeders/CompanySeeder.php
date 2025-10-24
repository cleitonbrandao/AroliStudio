<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar ou criar usuário de teste
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
            $company = Team::firstOrCreate(['name' => $companyData['name']], $companyData);
            
            // Verificar se o usuário já não pertence à empresa
            if (!$user->belongsToTeam($company)) {
                $company->addUser($user, 'owner');
            }
        }

        $this->command->info('Usuário de teste: teste@teste.com / password');
        $this->command->info('Empresas de teste criadas: ' . count($companies));
    }
}
