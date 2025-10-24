<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\People;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar usuário de teste
        $user = User::where('email', 'teste@teste.com')->first();

        if (!$user) {
            $this->command->warn('Usuário teste@teste.com não encontrado. Execute CompanySeeder primeiro.');
            return;
        }

        // Buscar primeiro team do usuário
        $team = $user->allTeams()->first();

        if (!$team) {
            $this->command->warn('Usuário não possui nenhum team. Execute CompanySeeder primeiro.');
            return;
        }

        $this->command->info("Criando customers para o team: {$team->name}");

        // Criar 20 customers com dados completos
        Customer::factory()
            ->count(20)
            ->forTeam($team)
            ->create();

        // Criar 5 customers sem CPF
        Customer::factory()
            ->count(5)
            ->forTeam($team)
            ->withoutCpf()
            ->create();

        // Criar 3 customers sem data de nascimento
        Customer::factory()
            ->count(3)
            ->forTeam($team)
            ->withoutBirthday()
            ->create();

        $total = Customer::where('team_id', $team->id)->count();
        
        $this->command->info("✅ {$total} customers criados com sucesso para o team: {$team->name}");
    }
}
