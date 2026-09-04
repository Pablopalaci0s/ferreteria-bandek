<?php

namespace Tests\Feature\Admin;

use App\Mail\ContrasenaTemporal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResetPasswordUsuarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_admin_puede_resetear_la_contrasena_de_un_usuario(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->vendedor()->create();

        $this->actingAs($admin)
            ->post(route('admin.usuarios.reset-password', $usuario))
            ->assertRedirect(route('admin.usuarios.index'));

        $usuario->refresh();

        $this->assertTrue($usuario->must_change_password);
        Mail::assertSent(ContrasenaTemporal::class, fn ($mail) => $mail->usuario->is($usuario));
    }

    public function test_si_falla_el_envio_de_correo_muestra_la_contrasena_temporal(): void
    {
        // Ej. el dominio de envio de Resend todavia no esta verificado y
        // solo deja mandar a la cuenta dueña de la API key: la contraseña
        // ya quedo cambiada, y el admin necesita ver cual es para pasarla
        // a mano en vez de recibir un 500 sin ninguna info.
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \Exception('Resend rechazó el envío'));

        $admin = User::factory()->admin()->create();
        $usuario = User::factory()->vendedor()->create();

        $respuesta = $this->actingAs($admin)
            ->post(route('admin.usuarios.reset-password', $usuario));

        $respuesta->assertRedirect(route('admin.usuarios.index'));
        $respuesta->assertSessionHas('error');

        $this->assertTrue($usuario->refresh()->must_change_password);
    }

    public function test_un_vendedor_no_puede_resetear_contrasenas(): void
    {
        Mail::fake();

        $vendedor = User::factory()->vendedor()->create();
        $otro = User::factory()->vendedor()->create();

        $this->actingAs($vendedor)
            ->post(route('admin.usuarios.reset-password', $otro))
            ->assertRedirect(route('admin.dashboard'));

        Mail::assertNothingSent();
    }

    public function test_un_usuario_con_contrasena_temporal_es_forzado_a_cambiarla(): void
    {
        $usuario = User::factory()->vendedor()->create([
            'must_change_password' => true,
        ]);

        $this->actingAs($usuario)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('password.obligatorio'));
    }

    public function test_al_cambiar_la_contrasena_se_levanta_el_bloqueo(): void
    {
        $usuario = User::factory()->vendedor()->create([
            'must_change_password' => true,
        ]);

        $this->actingAs($usuario)
            ->put(route('password.update'), [
                'current_password' => 'password',
                'password' => 'nueva-contrasena-segura',
                'password_confirmation' => 'nueva-contrasena-segura',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertFalse($usuario->refresh()->must_change_password);
    }
}
