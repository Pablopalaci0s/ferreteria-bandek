<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->words(2, true);

        return [
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(1, 999999),
            'activo' => true,
            'orden' => 0,
        ];
    }

    public function inactiva(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
