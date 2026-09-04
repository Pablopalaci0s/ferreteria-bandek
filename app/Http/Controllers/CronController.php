<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dispara tareas programadas por HTTP, para hostings sin cron real (ej.
 * Render free tier). Un cron externo gratuito (GitHub Actions, cron-job.org)
 * le pega a esta ruta una vez al día en vez de depender de
 * `schedule:run` + el cron del sistema operativo. Ver DEPLOY-CLOUD.md.
 */
class CronController extends Controller
{
    public function backup(Request $request): Response
    {
        $this->autorizar($request);

        Artisan::call('db:backup');

        return response(Artisan::output(), 200)->header('Content-Type', 'text/plain');
    }

    private function autorizar(Request $request): void
    {
        $secreto = (string) config('services.cron.secret');

        abort_if($secreto === '', 501, 'CRON_SECRET no está configurado.');

        abort_unless(
            hash_equals($secreto, (string) $request->header('X-Cron-Secret')),
            403
        );
    }
}
