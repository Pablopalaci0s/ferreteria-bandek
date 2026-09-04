<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CronBackupTest extends TestCase
{
    public function test_sin_cron_secret_configurado_devuelve_501(): void
    {
        config(['services.cron.secret' => '']);

        $this->getJson('/cron/backup')->assertStatus(501);
    }

    public function test_sin_la_cabecera_correcta_devuelve_403(): void
    {
        config(['services.cron.secret' => 'un-secreto']);

        $this->getJson('/cron/backup')->assertStatus(403);

        $this->withHeaders(['X-Cron-Secret' => 'otro-secreto'])
            ->getJson('/cron/backup')
            ->assertStatus(403);
    }

    public function test_con_la_cabecera_correcta_corre_el_backup(): void
    {
        config(['services.cron.secret' => 'un-secreto']);

        Artisan::shouldReceive('call')->once()->with('db:backup');
        Artisan::shouldReceive('output')->once()->andReturn('Backup creado.');

        $this->withHeaders(['X-Cron-Secret' => 'un-secreto'])
            ->get('/cron/backup')
            ->assertOk()
            ->assertSee('Backup creado.');
    }
}
