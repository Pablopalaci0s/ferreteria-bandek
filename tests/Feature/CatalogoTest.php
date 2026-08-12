<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_catalogo_carga_y_muestra_productos_activos(): void
    {
        $activo = Producto::factory()->create(['nombre' => 'Taladro Percutor']);
        $inactivo = Producto::factory()->inactivo()->create(['nombre' => 'Producto Oculto']);

        $respuesta = $this->get(route('catalogo.index'));

        $respuesta->assertOk();
        $respuesta->assertSee('Taladro Percutor');
        $respuesta->assertDontSee('Producto Oculto');
    }

    public function test_la_ficha_de_producto_activo_se_muestra(): void
    {
        $producto = Producto::factory()->create(['nombre' => 'Amoladora Angular']);

        $this->get(route('catalogo.show', $producto))
            ->assertOk()
            ->assertSee('Amoladora Angular');
    }

    public function test_la_ficha_de_un_producto_inactivo_da_404(): void
    {
        $producto = Producto::factory()->inactivo()->create();

        $this->get(route('catalogo.show', $producto))->assertNotFound();
    }

    public function test_buscar_filtra_por_termino(): void
    {
        Producto::factory()->create(['nombre' => 'Destornillador Phillips']);
        Producto::factory()->create(['nombre' => 'Llave Inglesa']);

        $respuesta = $this->getJson(route('catalogo.buscar', ['q' => 'Destornillador']));

        $respuesta->assertOk();
        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertContains('Destornillador Phillips', $nombres);
        $this->assertNotContains('Llave Inglesa', $nombres);
    }

    public function test_buscar_filtra_por_categoria(): void
    {
        $herramientas = Categoria::factory()->create(['slug' => 'herramientas']);
        $pinturas = Categoria::factory()->create(['slug' => 'pinturas']);

        Producto::factory()->create(['nombre' => 'Martillo', 'categoria_id' => $herramientas->id]);
        Producto::factory()->create(['nombre' => 'Latex Blanco', 'categoria_id' => $pinturas->id]);

        $respuesta = $this->getJson(route('catalogo.buscar', ['categoria' => 'herramientas']));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertContains('Martillo', $nombres);
        $this->assertNotContains('Latex Blanco', $nombres);
    }

    public function test_buscar_filtra_por_marca(): void
    {
        $bosch = Marca::factory()->create(['slug' => 'bosch']);
        $otra = Marca::factory()->create(['slug' => 'otra']);

        Producto::factory()->create(['nombre' => 'Sierra Bosch', 'marca_id' => $bosch->id]);
        Producto::factory()->create(['nombre' => 'Sierra Genérica', 'marca_id' => $otra->id]);

        $respuesta = $this->getJson(route('catalogo.buscar', ['marca' => 'bosch']));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertContains('Sierra Bosch', $nombres);
        $this->assertNotContains('Sierra Genérica', $nombres);
    }

    public function test_buscar_solo_disponibles_excluye_sin_stock(): void
    {
        Producto::factory()->create(['nombre' => 'Con Stock', 'stock' => 5]);
        Producto::factory()->sinStock()->create(['nombre' => 'Sin Stock']);

        $respuesta = $this->getJson(route('catalogo.buscar', ['disponible' => 1]));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertContains('Con Stock', $nombres);
        $this->assertNotContains('Sin Stock', $nombres);
    }

    public function test_buscar_solo_ofertas(): void
    {
        Producto::factory()->enOferta()->create(['nombre' => 'En Oferta']);
        Producto::factory()->create(['nombre' => 'Precio Normal', 'precio_oferta' => null]);

        $respuesta = $this->getJson(route('catalogo.buscar', ['oferta' => 1]));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertContains('En Oferta', $nombres);
        $this->assertNotContains('Precio Normal', $nombres);
    }

    public function test_buscar_no_devuelve_productos_inactivos(): void
    {
        Producto::factory()->inactivo()->create(['nombre' => 'Inactivo Oculto']);

        $respuesta = $this->getJson(route('catalogo.buscar', ['q' => 'Inactivo']));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre');
        $this->assertNotContains('Inactivo Oculto', $nombres);
    }

    public function test_buscar_ordena_por_precio_ascendente(): void
    {
        Producto::factory()->create(['nombre' => 'Caro', 'precio' => 9000]);
        Producto::factory()->create(['nombre' => 'Barato', 'precio' => 100]);
        Producto::factory()->create(['nombre' => 'Medio', 'precio' => 500]);

        $respuesta = $this->getJson(route('catalogo.buscar', ['orden' => 'precio_asc']));

        $nombres = collect($respuesta->json('productos'))->pluck('nombre')->all();
        $this->assertSame(['Barato', 'Medio', 'Caro'], $nombres);
    }

    public function test_el_inicio_muestra_los_productos_destacados(): void
    {
        Producto::factory()->destacado()->create(['nombre' => 'Producto Estrella']);
        Producto::factory()->create(['nombre' => 'Producto Comun']);

        $this->get(route('inicio'))
            ->assertOk()
            ->assertSee('Producto Estrella');
    }
}
