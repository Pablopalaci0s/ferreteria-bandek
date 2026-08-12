<?php

namespace Database\Factories;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VentaDetalle>
 */
class VentaDetalleFactory extends Factory
{
    protected $model = VentaDetalle::class;

    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 5);
        $precio = fake()->randomFloat(2, 100, 5000);

        return [
            'venta_id' => Venta::factory(),
            'producto_id' => Producto::factory(),
            'producto_nombre' => fake()->words(3, true),
            'producto_sku' => strtoupper(fake()->bothify('SKU-####')),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $precio * $cantidad,
        ];
    }
}
