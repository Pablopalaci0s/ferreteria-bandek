<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si un usuario tiene una contraseña temporal (must_change_password),
 * no lo dejamos usar el panel hasta que la cambie por una propia.
 */
class ObligarCambioPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->must_change_password) {
            return redirect()->route('password.obligatorio');
        }

        return $next($request);
    }
}
