<?php

use App\Http\Middleware\CabecerasSeguridad;
use App\Http\Middleware\EsAdmin;
use App\Http\Middleware\ObligarCambioPassword;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EsAdmin::class,
            'obligar.password' => ObligarCambioPassword::class,
        ]);

        $middleware->web(append: [
            CabecerasSeguridad::class,
        ]);

        // Detrás de un proxy/load balancer (Render, Cloudflare, etc.) que
        // termina el HTTPS y reenvía por HTTP interno: sin esto, Laravel
        // no sabe que el pedido original fue por HTTPS y genera URLs de
        // assets/enlaces con http:// (el navegador las bloquea por
        // "mixed content"). No hay una lista fija de IPs de estos
        // proveedores, así que se confía en cualquier proxy (estándar
        // para este tipo de hosting).
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
