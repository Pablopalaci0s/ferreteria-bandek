<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Venta>
 */
class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition(): array
    {
        return [
            'vendedor_id' => User::factory()->vendedor(),
            'estado' => Venta::ESTADO_PENDIENTE,
            'cliente_nombre' => fake()->name(),
            'cliente_telefono' => fake()->numerify('###-#######'),
            'total' => 0,
        ];
    }

    public function confirmada(): static
    {
        return $this->state(fn () => [
            'estado' => Venta::ESTADO_CONFIRMADA,
            'confirmada_en' => now(),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn () => [
            'estado' => Venta::ESTADO_CANCELADA,
            'cancelada_en' => now(),
        ]);
    }
}
