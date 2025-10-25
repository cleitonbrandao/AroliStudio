<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = \App\Models\Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => People::factory(),
            'team_id' => Team::factory(),
            'cpf' => fake('pt_BR')->cpf(false), // CPF sem formatação
            'email' => fake()->unique()->safeEmail(),
            'birthday' => fake()->dateTimeBetween('-80 years', '-18 years')->format('Y-m-d'),
        ];
    }

    /**
     * Indicar que o customer pertence a um team específico
     */
    public function forTeam(Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
            'person_id' => People::factory()->forTeam($team),
        ]);
    }

    /**
     * Indicar que o customer está vinculado a uma pessoa específica
     */
    public function forPerson(People $person): static
    {
        return $this->state(fn (array $attributes) => [
            'person_id' => $person->id,
            'team_id' => $person->team_id,
        ]);
    }

    /**
     * Customer sem CPF
     */
    public function withoutCpf(): static
    {
        return $this->state(fn (array $attributes) => [
            'cpf' => null,
        ]);
    }

    /**
     * Customer sem data de nascimento
     */
    public function withoutBirthday(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthday' => null,
        ]);
    }
}
