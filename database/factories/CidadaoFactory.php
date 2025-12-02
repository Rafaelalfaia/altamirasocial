<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cidadao>
 */
class CidadaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->name(),
            'cpf' => $this->faker->unique()->numerify('###.###.###-##'),
            'status' => $this->faker->randomElement(['pendente', 'aprovado', 'recusado']),
            'bairro' => $this->faker->city(),
            'renda' => $this->faker->randomFloat(2, 300, 3000),
        ];
    }
}
