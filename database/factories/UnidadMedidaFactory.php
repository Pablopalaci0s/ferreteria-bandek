<?php

namespace Database\Factories;

use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnidadMedida>
 */
class UnidadMedidaFactory extends Factory
{
    protected $model = UnidadMedida::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement(['Unidad', 'Metro', 'Kilogramo', 'Litro', 'Caja']),
            'abreviatura' => fake()->randomElement(['un', 'm', 'kg', 'lt', 'cja']),
        ];
    }
}
