<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SolicitudPrecio;
use Illuminate\Http\Request;

class SolicitudPrecioController extends Controller
{
    public function index()
    {
        $pendientes = SolicitudPrecio::with(['producto', 'solicitante'])
            ->pendientes()
            ->latest()
            ->get();

        $historial = SolicitudPrecio::with(['producto', 'solicitante', 'revisor'])
            ->whereIn('estado', ['aprobado', 'rechazado'])
            ->latest('revisado_en')
            ->limit(30)
            ->get();

        return view('admin.solicitudes-precio.index', compact('pendientes', 'historial'));
    }

    public function aprobar(Request $request, SolicitudPrecio $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue revisada.');
        }

        $producto = $solicitud->producto;

        $producto->update([
            'precio' => $solicitud->precio_nuevo,
            'precio_oferta' => $solicitud->precio_oferta_nuevo,
        ]);

        $solicitud->update([
            'estado' => 'aprobado',
            'revisado_por' => $request->user()->id,
            'revisado_en' => now(),
        ]);

        return back()->with('status', 'Precio aprobado y actualizado en el producto.');
    }

    public function rechazar(Request $request, SolicitudPrecio $solicitud)
    {
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Esta solicitud ya fue revisada.');
        }

        $solicitud->update([
            'estado' => 'rechazado',
            'revisado_por' => $request->user()->id,
            'revisado_en' => now(),
        ]);

        return back()->with('status', 'Solicitud rechazada. El precio del producto no cambió.');
    }
}
