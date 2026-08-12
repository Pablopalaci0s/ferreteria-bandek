<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Agrega cabeceras de seguridad a todas las respuestas.
 *
 * - X-Frame-Options: evita que el sitio se embeba en un iframe (clickjacking).
 * - X-Content-Type-Options: evita que el navegador "adivine" el tipo de archivo
 *   (MIME sniffing), otra vía de ejecución de contenido malicioso.
 * - Referrer-Policy: no filtra la URL completa a sitios externos.
 * - Permissions-Policy: apaga APIs del navegador que el sitio no usa.
 */
class CabecerasSeguridad
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
