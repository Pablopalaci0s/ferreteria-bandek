<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $nombre = fake()->unique()->words(3, true);

        return [
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'modelo' => fake()->bothify('MOD-###'),
            'nombre' => ucfirst($nombre),
            'slug' => Str::slug($nombre).'-'.fake()->unique()->numberBetween(1, 999999),
            'descripcion' => fake()->sentence(),
            'descripcion_larga' => fake()->paragraph(),
            'precio' => fake()->randomFloat(2, 100, 10000),
            'precio_oferta' => null,
            'costo' => fake()->randomFloat(2, 50, 5000),
            'stock' => fake()->numberBetween(0, 100),
            'stock_minimo' => 5,
            'categoria_id' => Categoria::factory(),
            'marca_id' => Marca::factory(),
            'unidad_medida_id' => UnidadMedida::factory(),
            'activo' => true,
            'destacado' => false,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }

    public function destacado(): static
    {
        return $this->state(fn () => ['destacado' => true]);
    }

    public function sinStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function enOferta(): static
    {
        return $this->state(fn (array $attributes) => [
            'precio_oferta' => round($attributes['precio'] * 0.8, 2),
        ]);
    }
}
