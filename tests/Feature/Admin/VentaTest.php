<?php

namespace Tests\Feature\Admin;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_vendedor_puede_registrar_una_venta_pendiente_sin_descontar_stock(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 100, 'stock' => 10]);

        $this->actingAs($vendedor)
            ->post(route('admin.ventas.store'), [
                'cliente_nombre' => 'Juan Cliente',
                'cliente_telefono' => '099-1234567',
                'items' => [
                    ['producto_id' => $producto->id, 'cantidad' => 3],
                ],
            ])
            ->assertRedirect();

        $venta = Venta::first();
        $this->assertNotNull($venta);
        $this->assertSame('pendiente', $venta->estado);
        $this->assertEquals($vendedor->id, $venta->vendedor_id);
        $this->assertSame('Juan Cliente', $venta->cliente_nombre);

        // Pendiente NO descuenta stock ni genera movimientos.
        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_se_pueden_agregar_varios_productos_a_una_venta(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $p1 = Producto::factory()->create(['precio' => 100, 'stock' => 10]);
        $p2 = Producto::factory()->create(['precio' => 250, 'stock' => 10]);

        $this->actingAs($vendedor)
            ->post(route('admin.ventas.store'), [
                'items' => [
                    ['producto_id' => $p1->id, 'cantidad' => 2],
                    ['producto_id' => $p2->id, 'cantidad' => 1],
                ],
            ]);

        $venta = Venta::with('detalles')->first();
        $this->assertCount(2, $venta->detalles);
    }

    public function test_calcula_correctamente_subtotales_y_total_usando_precio_del_servidor(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        // Producto en oferta: el precio final autoritativo es 80, no 100.
        $p1 = Producto::factory()->create(['precio' => 100, 'precio_oferta' => 80, 'stock' => 10]);
        $p2 = Producto::factory()->create(['precio' => 250, 'precio_oferta' => null, 'stock' => 10]);

        $this->actingAs($vendedor)
            ->post(route('admin.ventas.store'), [
                'items' => [
                    // Aunque el cliente mandara otro precio, se ignora.
                    ['producto_id' => $p1->id, 'cantidad' => 2, 'precio' => 5],
                    ['producto_id' => $p2->id, 'cantidad' => 3],
                ],
            ]);

        $venta = Venta::with('detalles')->first();

        $d1 = $venta->detalles->firstWhere('producto_id', $p1->id);
        $d2 = $venta->detalles->firstWhere('producto_id', $p2->id);

        $this->assertEquals(80, $d1->precio_unitario);
        $this->assertEquals(160, $d1->subtotal);   // 80 * 2
        $this->assertEquals(750, $d2->subtotal);    // 250 * 3
        $this->assertEquals(910, $venta->total);    // 160 + 750
    }

    public function test_confirmar_una_venta_descuenta_stock_y_registra_movimientos(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 100, 'stock' => 10]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 4,
            'precio_unitario' => 100,
            'subtotal' => 400,
        ]);

        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertRedirect(route('admin.ventas.show', $venta));

        $this->assertSame(6, $producto->fresh()->stock);           // 10 - 4
        $this->assertSame('confirmada', $venta->fresh()->estado);
        $this->assertEquals($vendedor->id, $venta->fresh()->confirmada_por);
        $this->assertNotNull($venta->fresh()->confirmada_en);

        $this->assertDatabaseHas('movimientos_inventario', [
            'producto_id' => $producto->id,
            'venta_id' => $venta->id,
            'tipo' => 'venta',
            'cantidad' => 4,
        ]);
    }

    public function test_no_confirma_si_no_hay_stock_suficiente(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 3]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 5,
            'precio_unitario' => 100,
            'subtotal' => 500,
        ]);

        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertSessionHas('error');

        // Sigue pendiente, sin tocar stock ni movimientos.
        $this->assertSame('pendiente', $venta->fresh()->estado);
        $this->assertSame(3, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_cancelar_una_venta_pendiente_no_descuenta_stock(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 4,
            'precio_unitario' => 100,
            'subtotal' => 400,
        ]);

        $this->actingAs($vendedor)
            ->put(route('admin.ventas.cancelar', $venta))
            ->assertRedirect(route('admin.ventas.show', $venta));

        $this->assertSame('cancelada', $venta->fresh()->estado);
        $this->assertEquals($vendedor->id, $venta->fresh()->cancelada_por);
        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_confirmar_dos_veces_no_descuenta_stock_dos_veces(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 10]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 4,
            'precio_unitario' => 100,
            'subtotal' => 400,
        ]);

        // Primera confirmación: descuenta.
        $this->actingAs($vendedor)->put(route('admin.ventas.confirmar', $venta));
        // Segunda confirmación: debe ser rechazada por el guard de idempotencia.
        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertSessionHas('error');

        $this->assertSame(6, $producto->fresh()->stock); // solo se descontó una vez
        $this->assertDatabaseCount('movimientos_inventario', 1);
    }

    public function test_una_venta_con_una_linea_sin_stock_no_descuenta_ninguna_linea(): void
    {
        // Atomicidad: si una línea falla, ninguna se aplica (rollback).
        $vendedor = User::factory()->vendedor()->create();
        $ok = Producto::factory()->create(['stock' => 10]);
        $sinStock = Producto::factory()->create(['stock' => 1]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();

        $venta->detalles()->createMany([
            [
                'producto_id' => $ok->id,
                'producto_nombre' => $ok->nombre,
                'producto_sku' => $ok->sku,
                'cantidad' => 5,
                'precio_unitario' => 100,
                'subtotal' => 500,
            ],
            [
                'producto_id' => $sinStock->id,
                'producto_nombre' => $sinStock->nombre,
                'producto_sku' => $sinStock->sku,
                'cantidad' => 5,
                'precio_unitario' => 100,
                'subtotal' => 500,
            ],
        ]);

        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertSessionHas('error');

        // Ninguna línea se descontó y no hay movimientos.
        $this->assertSame(10, $ok->fresh()->stock);
        $this->assertSame(1, $sinStock->fresh()->stock);
        $this->assertSame('pendiente', $venta->fresh()->estado);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_un_error_dentro_de_la_transaccion_hace_rollback_del_stock(): void
    {
        // Prueba directa de la garantía transaccional de registrarMovimiento:
        // si la transacción externa lanza, el descuento se revierte.
        $producto = Producto::factory()->create(['stock' => 10]);

        try {
            DB::transaction(function () use ($producto) {
                $producto->registrarMovimiento('venta', 4, 'prueba', null);
                throw new \RuntimeException('fallo simulado a mitad de la transacción');
            });
        } catch (\RuntimeException $e) {
            // esperado
        }

        $this->assertSame(10, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 0);
    }

    public function test_dos_ventas_del_mismo_stock_solo_una_se_confirma(): void
    {
        // Stock = 1. Dos vendedores con una venta de 1 cada uno.
        // Solo una debe poder confirmarse; la otra recibe "stock insuficiente".
        // (En MySQL el lockForUpdate serializa la concurrencia real; acá se
        //  verifica la lógica de forma secuencial.)
        $producto = Producto::factory()->create(['stock' => 1]);

        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();

        $ventaA = Venta::factory()->for($vendedorA, 'vendedor')->create();
        $ventaB = Venta::factory()->for($vendedorB, 'vendedor')->create();

        foreach ([$ventaA, $ventaB] as $v) {
            $v->detalles()->create([
                'producto_id' => $producto->id,
                'producto_nombre' => $producto->nombre,
                'producto_sku' => $producto->sku,
                'cantidad' => 1,
                'precio_unitario' => 100,
                'subtotal' => 100,
            ]);
        }

        $this->actingAs($vendedorA)
            ->put(route('admin.ventas.confirmar', $ventaA))
            ->assertRedirect(route('admin.ventas.show', $ventaA));

        $this->actingAs($vendedorB)
            ->put(route('admin.ventas.confirmar', $ventaB))
            ->assertSessionHas('error');

        $this->assertSame('confirmada', $ventaA->fresh()->estado);
        $this->assertSame('pendiente', $ventaB->fresh()->estado);
        $this->assertSame(0, $producto->fresh()->stock);
        $this->assertDatabaseCount('movimientos_inventario', 1);
    }

    public function test_un_vendedor_no_puede_ver_la_venta_de_otro_vendedor(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        $venta = Venta::factory()->for($vendedorB, 'vendedor')->create();

        $this->actingAs($vendedorA)
            ->get(route('admin.ventas.show', $venta))
            ->assertForbidden();
    }

    public function test_un_admin_puede_ver_cualquier_venta(): void
    {
        $admin = User::factory()->admin()->create();
        $venta = Venta::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.ventas.show', $venta))
            ->assertOk();
    }

    public function test_un_vendedor_no_puede_acceder_a_funciones_de_administrador(): void
    {
        $vendedor = User::factory()->vendedor()->create();

        // Rutas gateadas por el middleware admin.
        $this->actingAs($vendedor)
            ->get(route('admin.usuarios.index'))
            ->assertRedirect(route('admin.dashboard'));

        $this->actingAs($vendedor)
            ->get(route('admin.configuracion.edit'))
            ->assertRedirect(route('admin.dashboard'));
    }
}
