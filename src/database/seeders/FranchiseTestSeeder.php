<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Services\FranchiseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FranchiseTestSeeder extends Seeder
{
    public function run(): void
    {
        $franchiseService = app(FranchiseService::class);

        // 1. Criar CEO da Matriz
        $ceo = User::firstOrCreate(
            ['email' => 'joao@matriz.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Criar Matriz
        $matriz = Company::create([
            'name' => 'Restaurante Matriz Ltda',
            'user_id' => $ceo->id,
            'personal_team' => false,
            'max_users' => 50,
            'current_users' => 1,
            'plan_type' => 'premium',
            'is_active' => true,
        ]);

        // Adicionar CEO como ceo da matriz
        $matriz->addUserWithCount($ceo, 'ceo');

        // 3. Criar Gerente Regional
        $gerente = User::firstOrCreate(
            ['email' => 'ana@matriz.com'],
            [
                'name' => 'Ana Costa',
                'password' => Hash::make('password'),
            ]
        );

        // Adicionar gerente como regional_manager da matriz
        $matriz->addUserWithCount($gerente, 'regional_manager');

        // 4. Criar Franqueada 1
        $franqueada1 = User::firstOrCreate(
            ['email' => 'maria@filial.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
            ]
        );

        // Criar Filial 1
        $filial1 = $franchiseService->createFranchise($matriz, $franqueada1, [
            'name' => 'Restaurante Filial Centro',
            'max_users' => 15,
            'matrix_managers' => [$gerente->id],
        ]);

        // 5. Criar Franqueado 2
        $franqueado2 = User::firstOrCreate(
            ['email' => 'carlos@filial.com'],
            [
                'name' => 'Carlos Oliveira',
                'password' => Hash::make('password'),
            ]
        );

        // Criar Filial 2
        $filial2 = $franchiseService->createFranchise($matriz, $franqueado2, [
            'name' => 'Restaurante Filial Shopping',
            'max_users' => 20,
            'matrix_managers' => [$gerente->id],
        ]);

        // 6. Adicionar alguns funcionários nas filiais
        $funcionario1 = User::firstOrCreate(
            ['email' => 'pedro@filial.com'],
            [
                'name' => 'Pedro Funcionário',
                'password' => Hash::make('password'),
            ]
        );

        $funcionario2 = User::firstOrCreate(
            ['email' => 'sofia@filial.com'],
            [
                'name' => 'Sofia Funcionária',
                'password' => Hash::make('password'),
            ]
        );

        // Adicionar funcionários como employees
        $filial1->addUserWithCount($funcionario1, 'employee');
        $filial2->addUserWithCount($funcionario2, 'employee');

        echo "✅ Sistema de Franquias com Nova Hierarquia criado com sucesso!\n";
        echo "\n📊 Estrutura criada:\n";
        echo "🏢 Matriz: Restaurante Matriz Ltda\n";
        echo "   👑 CEO: João Silva (joao@matriz.com)\n";
        echo "   ⚙️ Gerente Regional: Ana Costa (ana@matriz.com)\n";
        echo "\n🏪 Filial 1: Restaurante Filial Centro\n";
        echo "   🏪 Franqueada: Maria Santos (maria@filial.com)\n";
        echo "   ⚙️ Gerente Regional: Ana Costa\n";
        echo "   👤 Funcionário: Pedro Funcionário\n";
        echo "\n🏪 Filial 2: Restaurante Filial Shopping\n";
        echo "   🏪 Franqueado: Carlos Oliveira (carlos@filial.com)\n";
        echo "   ⚙️ Gerente Regional: Ana Costa\n";
        echo "   👤 Funcionária: Sofia Funcionária\n";
        echo "\n🔑 Senha para todos os usuários: password\n";
        echo "\n📋 Nova Hierarquia de Roles:\n";
        echo "   👑 CEO (90) - Controle total da matriz\n";
        echo "   🏪 Franqueado (80) - Gerencia filial\n";
        echo "   ⚙️ Gerente Regional (70) - Supervisiona filiais\n";
        echo "   👤 Funcionário (30) - Acesso básico\n";
    }
}
