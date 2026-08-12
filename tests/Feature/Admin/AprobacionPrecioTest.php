<?php

namespace Tests\Feature\Admin;

use App\Models\Producto;
use App\Models\SolicitudPrecio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AprobacionPrecioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Arma un payload válido para actualizar un producto, partiendo de sus
     * datos actuales y permitiendo sobrescribir campos puntuales (p. ej. precio).
     */
    private function payloadProducto(Producto $producto, array $overrides = []): array
    {
        return array_merge([
            'sku' => $producto->sku,
            'modelo' => $producto->modelo,
            'nombre' => $producto->nombre,
            'descripcion' => $producto->descripcion,
            'descripcion_larga' => $producto->descripcion_larga,
            'precio' => (string) $producto->precio,
            'precio_oferta' => $producto->precio_oferta ? (string) $producto->precio_oferta : null,
            'costo' => (string) $producto->costo,
            'stock_minimo' => $producto->stock_minimo,
            'categoria_id' => $producto->categoria_id,
            'marca_id' => $producto->marca_id,
            'unidad_medida_id' => $producto->unidad_medida_id,
            'activo' => $producto->activo ? 1 : 0,
            'destacado' => $producto->destacado ? 1 : 0,
        ], $overrides);
    }

    public function test_vendedor_no_cambia_el_precio_directo_sino_que_crea_una_solicitud(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $this->actingAs($vendedor)
            ->put(route('admin.productos.update', $producto), $this->payloadProducto($producto, [
                'precio' => 1500,
            ]))
            ->assertRedirect(route('admin.productos.index'));

        // El precio del producto NO cambió todavía.
        $this->assertEquals(1000, $producto->fresh()->precio);

        // Se registró una solicitud pendiente con los valores correctos.
        $this->assertDatabaseHas('solicitudes_precio', [
            'producto_id' => $producto->id,
            'usuario_id' => $vendedor->id,
            'precio_actual' => 1000,
            'precio_nuevo' => 1500,
            'estado' => 'pendiente',
        ]);
    }

    public function test_vendedor_puede_cambiar_datos_no_precio_sin_generar_solicitud(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 1000, 'nombre' => 'Martillo viejo']);

        $this->actingAs($vendedor)
            ->put(route('admin.productos.update', $producto), $this->payloadProducto($producto, [
                'nombre' => 'Martillo nuevo',
            ]))
            ->assertRedirect(route('admin.productos.index'));

        $this->assertSame('Martillo nuevo', $producto->fresh()->nombre);
        $this->assertDatabaseCount('solicitudes_precio', 0);
    }

    public function test_admin_cambia_el_precio_directamente_sin_solicitud(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $this->actingAs($admin)
            ->put(route('admin.productos.update', $producto), $this->payloadProducto($producto, [
                'precio' => 2000,
            ]))
            ->assertRedirect(route('admin.productos.index'));

        $this->assertEquals(2000, $producto->fresh()->precio);
        $this->assertDatabaseCount('solicitudes_precio', 0);
    }

    public function test_reeditar_precio_con_solicitud_pendiente_actualiza_la_existente(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        // Primera solicitud: 1000 -> 1500
        $this->actingAs($vendedor)
            ->put(route('admin.productos.update', $producto), $this->payloadProducto($producto, ['precio' => 1500]));

        // Segunda solicitud sobre el mismo producto: 1000 -> 1800
        $this->actingAs($vendedor)
            ->put(route('admin.productos.update', $producto), $this->payloadProducto($producto, ['precio' => 1800]));

        // No se acumulan solicitudes: sigue habiendo una sola, con el valor nuevo.
        $this->assertDatabaseCount('solicitudes_precio', 1);
        $this->assertDatabaseHas('solicitudes_precio', [
            'producto_id' => $producto->id,
            'precio_nuevo' => 1800,
            'estado' => 'pendiente',
        ]);
    }

    public function test_admin_aprueba_solicitud_y_se_aplica_el_precio(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $solicitud = SolicitudPrecio::create([
            'producto_id' => $producto->id,
            'usuario_id' => User::factory()->vendedor()->create()->id,
            'precio_actual' => 1000,
            'precio_nuevo' => 1500,
            'precio_oferta_actual' => null,
            'precio_oferta_nuevo' => null,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.solicitudes-precio.aprobar', $solicitud))
            ->assertRedirect();

        $this->assertEquals(1500, $producto->fresh()->precio);
        $this->assertSame('aprobado', $solicitud->fresh()->estado);
        $this->assertEquals($admin->id, $solicitud->fresh()->revisado_por);
        $this->assertNotNull($solicitud->fresh()->revisado_en);
    }

    public function test_admin_rechaza_solicitud_y_el_precio_no_cambia(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $solicitud = SolicitudPrecio::create([
            'producto_id' => $producto->id,
            'usuario_id' => User::factory()->vendedor()->create()->id,
            'precio_actual' => 1000,
            'precio_nuevo' => 1500,
            'estado' => 'pendiente',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.solicitudes-precio.rechazar', $solicitud))
            ->assertRedirect();

        $this->assertEquals(1000, $producto->fresh()->precio);
        $this->assertSame('rechazado', $solicitud->fresh()->estado);
    }

    public function test_no_se_puede_aprobar_una_solicitud_ya_revisada(): void
    {
        $admin = User::factory()->admin()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $solicitud = SolicitudPrecio::create([
            'producto_id' => $producto->id,
            'usuario_id' => User::factory()->vendedor()->create()->id,
            'precio_actual' => 1000,
            'precio_nuevo' => 1500,
            'estado' => 'rechazado',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.solicitudes-precio.aprobar', $solicitud))
            ->assertSessionHas('error');

        // Al estar rechazada, aprobar no debe tocar el precio.
        $this->assertEquals(1000, $producto->fresh()->precio);
        $this->assertSame('rechazado', $solicitud->fresh()->estado);
    }

    public function test_un_vendedor_no_puede_aprobar_solicitudes(): void
    {
        $vendedor = User::factory()->vendedor()->create();
        $producto = Producto::factory()->create(['precio' => 1000]);

        $solicitud = SolicitudPrecio::create([
            'producto_id' => $producto->id,
            'usuario_id' => $vendedor->id,
            'precio_actual' => 1000,
            'precio_nuevo' => 1500,
            'estado' => 'pendiente',
        ]);

        // El middleware admin redirige a los que no son admin; el precio no cambia.
        $this->actingAs($vendedor)
            ->put(route('admin.solicitudes-precio.aprobar', $solicitud))
            ->assertRedirect();

        $this->assertEquals(1000, $producto->fresh()->precio);
        $this->assertSame('pendiente', $solicitud->fresh()->estado);
    }

    public function test_invitado_no_puede_editar_productos(): void
    {
        $producto = Producto::factory()->create();

        $this->put(route('admin.productos.update', $producto), $this->payloadProducto($producto))
            ->assertRedirect(route('login'));
    }
}
