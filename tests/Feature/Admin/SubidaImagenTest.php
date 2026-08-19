<?php

namespace Tests\Feature\Admin;

use App\Models\Banner;
use App\Models\User;
use App\Support\ImagenOptimizada;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubidaImagenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_rechaza_un_svg_por_riesgo_de_xss(): void
    {
        $admin = User::factory()->admin()->create();

        $svg = UploadedFile::fake()->create('malicioso.svg', 5, 'image/svg+xml');

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), ['zona' => 'hero_principal', 'imagen' => $svg])
            ->assertSessionHasErrors('imagen');

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_rechaza_una_imagen_con_dimensiones_excesivas(): void
    {
        $admin = User::factory()->admin()->create();

        // Ancho por encima del máximo permitido (decompression bomb / DoS).
        $gigante = UploadedFile::fake()->image('bomba.jpg', 6001, 10);

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), ['zona' => 'hero_principal', 'imagen' => $gigante])
            ->assertSessionHasErrors('imagen');

        $this->assertDatabaseCount('banners', 0);
    }

    public function test_una_imagen_valida_se_guarda_como_webp(): void
    {
        $admin = User::factory()->admin()->create();

        $imagen = UploadedFile::fake()->image('foto.jpg', 800, 600);

        $this->actingAs($admin)
            ->post(route('admin.banners.store'), ['zona' => 'hero_principal', 'imagen' => $imagen])
            ->assertRedirect(route('admin.banners.index', ['zona' => 'hero_principal']));

        $banner = Banner::first();
        $this->assertNotNull($banner);
        $this->assertStringEndsWith('.webp', $banner->imagen);
        Storage::disk('public')->assertExists($banner->imagen);
    }

    public function test_guardar_genera_webp_y_thumbnail(): void
    {
        $imagen = UploadedFile::fake()->image('producto.jpg', 2000, 2000);

        $ruta = ImagenOptimizada::guardar($imagen, 'productos', 1400, 1400, 82, thumbnail: 500);

        // Imagen principal en WebP.
        $this->assertStringEndsWith('.webp', $ruta);
        Storage::disk('public')->assertExists($ruta);

        // Thumbnail en {carpeta}/thumbs/.
        $rutaThumb = ImagenOptimizada::thumb($ruta);
        $this->assertStringContainsString('productos/thumbs/', $rutaThumb);
        Storage::disk('public')->assertExists($rutaThumb);

        // El thumbnail no supera los 500px de lado.
        [$ancho, $alto] = getimagesize(Storage::disk('public')->path($rutaThumb));
        $this->assertLessThanOrEqual(500, $ancho);
        $this->assertLessThanOrEqual(500, $alto);
    }

    public function test_la_imagen_principal_respeta_el_ancho_maximo(): void
    {
        $imagen = UploadedFile::fake()->image('grande.jpg', 3000, 1500);

        $ruta = ImagenOptimizada::guardar($imagen, 'productos', 1400, 1400, 82);

        [$ancho] = getimagesize(Storage::disk('public')->path($ruta));
        $this->assertLessThanOrEqual(1400, $ancho);
    }
}
