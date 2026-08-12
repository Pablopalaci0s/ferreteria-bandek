<?php

namespace Database\Factories;

use App\Models\Marca;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Marca>
 */
class MarcaFactory extends Factory
{
    protected $model = Marca::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->company();

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(1, 999999),
            'activo' => true,
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
