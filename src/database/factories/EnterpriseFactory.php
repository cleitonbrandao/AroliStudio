<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enterprise>
 */
class EnterpriseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_fantasia' => fake('pt_BR')->name,
            'razao_social' => fake('pt_BR')->name,
            'cnpj' => fake('pt_BR')->cnpj(false),
            'inscricao_estatual' => fake()->regexify('[A-Z]{5}[0-4]{3}'),
            'bussines_email' => fake()->unique()->safeEmail()
        ];
    }
}
