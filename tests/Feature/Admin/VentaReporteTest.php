<?php

namespace Tests\Feature\Admin;

use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VentaReporteTest extends TestCase
{
    use RefreshDatabase;

    private function ventaConfirmada(User $vendedor, float $total, array $lineas = [], ?\DateTimeInterface $cuando = null): Venta
    {
        $venta = Venta::factory()->confirmada()->for($vendedor, 'vendedor')->create([
            'total' => $total,
            'confirmada_en' => $cuando ?? now(),
        ]);

        foreach ($lineas as $linea) {
            $venta->detalles()->create(array_merge([
                'producto_nombre' => 'Producto',
                'producto_sku' => 'SKU-1',
                'cantidad' => 1,
                'precio_unitario' => $total,
                'subtotal' => $total,
            ], $linea));
        }

        return $venta;
    }

    public function test_el_reporte_suma_solo_ventas_confirmadas_del_rango(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedor = User::factory()->vendedor()->create();

        $this->ventaConfirmada($vendedor, 500);
        $this->ventaConfirmada($vendedor, 300);

        // Pendiente: no cuenta.
        Venta::factory()->for($vendedor, 'vendedor')->create(['total' => 999]);
        // Confirmada pero fuera del rango (mes pasado): no cuenta.
        $this->ventaConfirmada($vendedor, 777, [], now()->subMonth());

        $res = $this->actingAs($admin)->get(route('admin.ventas.reportes'));

        $res->assertOk();
        $this->assertEquals(800, $res->viewData('totalVentas'));
        $this->assertEquals(2, $res->viewData('cantidadVentas'));
    }

    public function test_el_reporte_lista_productos_mas_vendidos(): void
    {
        $admin = User::factory()->admin()->create();
        $vendedor = User::factory()->vendedor()->create();

        $this->ventaConfirmada($vendedor, 200, [
            ['producto_nombre' => 'Martillo', 'cantidad' => 2, 'precio_unitario' => 100, 'subtotal' => 200],
        ]);
        $this->ventaConfirmada($vendedor, 500, [
            ['producto_nombre' => 'Taladro', 'cantidad' => 5, 'precio_unitario' => 100, 'subtotal' => 500],
        ]);

        $res = $this->actingAs($admin)->get(route('admin.ventas.reportes'));

        $top = $res->viewData('productosMasVendidos');
        $this->assertSame('Taladro', $top->first()->producto_nombre); // 5 unidades
        $this->assertEquals(5, $top->first()->unidades);
    }

    public function test_un_vendedor_solo_ve_sus_propias_ventas_en_el_reporte(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();

        $this->ventaConfirmada($vendedorA, 500);
        $this->ventaConfirmada($vendedorB, 300);

        $res = $this->actingAs($vendedorA)->get(route('admin.ventas.reportes'));

        // Solo la venta propia (500), no la de B.
        $this->assertEquals(500, $res->viewData('totalVentas'));
        $this->assertEquals(1, $res->viewData('cantidadVentas'));
    }

    public function test_el_comprobante_de_una_venta_se_muestra(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $venta = $this->ventaConfirmada($vendedor, 150, [
            ['producto_nombre' => 'Destornillador', 'cantidad' => 1, 'precio_unitario' => 150, 'subtotal' => 150],
        ]);

        $this->actingAs($vendedor)
            ->get(route('admin.ventas.ticket', $venta))
            ->assertOk()
            ->assertSee('Destornillador')
            ->assertSee('BANDEK');
    }

    public function test_un_vendedor_no_puede_ver_el_comprobante_de_otro(): void
    {
        $vendedorA = User::factory()->vendedor()->create();
        $vendedorB = User::factory()->vendedor()->create();
        $venta = $this->ventaConfirmada($vendedorB, 150);

        $this->actingAs($vendedorA)
            ->get(route('admin.ventas.ticket', $venta))
            ->assertForbidden();
    }

    public function test_confirmar_una_venta_que_deja_stock_bajo_el_minimo_avisa(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 6, 'stock_minimo' => 5]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 2,
            'precio_unitario' => 100,
            'subtotal' => 200,
        ]);

        // 6 - 2 = 4, que es <= 5 (mínimo) -> debe avisar.
        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertSessionHas('alerta_stock');
    }

    public function test_confirmar_sin_dejar_stock_bajo_no_avisa(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['stock' => 20, 'stock_minimo' => 5]);
        $venta = Venta::factory()->for($vendedor, 'vendedor')->create();
        $venta->detalles()->create([
            'producto_id' => $producto->id,
            'producto_nombre' => $producto->nombre,
            'producto_sku' => $producto->sku,
            'cantidad' => 2,
            'precio_unitario' => 100,
            'subtotal' => 200,
        ]);

        // 20 - 2 = 18, muy por encima del mínimo -> sin aviso.
        $this->actingAs($vendedor)
            ->put(route('admin.ventas.confirmar', $venta))
            ->assertSessionMissing('alerta_stock');
    }
}
