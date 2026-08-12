<?php

namespace Tests\Feature\Admin;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_una_entrada_suma_al_stock(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => 5,
                'motivo' => 'Compra a proveedor',
            ])
            ->assertRedirect(route('admin.inventario.index'));

        $this->assertSame(15, $producto->fresh()->stock);
        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id,
            'usuario_id' => $usuario->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
            'motivo' => 'Compra a proveedor',
        ]);
    }

    public function test_una_devolucion_suma_al_stock(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'devolucion',
                'cantidad' => 3,
                'motivo' => 'Cliente devolvió',
            ])
            ->assertRedirect(route('admin.inventario.index'));

        $this->assertSame(13, $producto->fresh()->stock);
    }

    public function test_una_perdida_resta_del_stock(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'perdida',
                'cantidad' => 4,
                'motivo' => 'Producto dañado',
            ])
            ->assertRedirect(route('admin.inventario.index'));

        $this->assertSame(6, $producto->fresh()->stock);
    }

    public function test_un_ajuste_fija_el_stock_al_valor_indicado(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'ajuste',
                'cantidad' => 3,
                'motivo' => 'Conteo físico',
            ]);

        $this->assertSame(3, $producto->fresh()->stock);
    }

    public function test_no_se_puede_registrar_una_perdida_mayor_al_stock(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 3]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'perdida',
                'cantidad' => 5,
            ])
            ->assertSessionHasErrors('cantidad');

        // El stock no cambió y no se registró ningún movimiento.
        $this->assertSame(3, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_no_se_puede_registrar_una_salida_manual(): void
    {
        // Las salidas se generan solo al confirmar una venta.
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'salida',
                'cantidad' => 2,
            ])
            ->assertSessionHasErrors('tipo');

        $this->assertSame(10, $producto->fresh()->stock);
    }

    public function test_el_tipo_de_movimiento_debe_ser_valido(): void
    {
        $usuario = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->actingAs($usuario)
            ->post(route('admin.inventario.store'), [
                'producto_id' => $producto->id,
                'tipo' => 'robo',
                'cantidad' => 1,
            ])
            ->assertSessionHasErrors('tipo');

        $this->assertSame(10, $producto->fresh()->stock);
    }

    public function test_invitado_no_puede_registrar_movimientos(): void
    {
        $producto = Producto::factory()->create(['stock' => 10]);

        $this->post(route('admin.inventario.store'), [
            'producto_id' => $producto->id,
            'tipo' => 'entrada',
            'cantidad' => 5,
        ])->assertRedirect(route('login'));

        $this->assertSame(10, $producto->fresh()->stock);
    }
}
