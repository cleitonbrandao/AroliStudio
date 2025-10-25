<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People>
 */
class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => \App\Models\Team::factory(),
            'name' => fake('pt_BR')->firstName(),
            'last_name' => fake('pt_BR')->lastName(),
            'phone' => fake('pt_BR')->cellphoneNumber(),
            'photo' => null,
        ];
    }

    /**
     * Indicar que a pessoa pertence a um team específico
     */
    public function forTeam(\App\Models\Team $team): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $team->id,
        ]);
    }
}
