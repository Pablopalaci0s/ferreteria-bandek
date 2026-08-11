<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->rol !== 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'No tenés permiso para acceder a esa sección.');
        }

        return $next($request);
    }
}