<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Claves de configuración que maneja esta pantalla.
     */
    private const CLAVES = ['whatsapp_numero', 'telefono', 'direccion'];

    public function edit()
    {
        $config = Configuracion::whereIn('clave', self::CLAVES)->pluck('valor', 'clave');

        return view('admin.configuracion.edit', [
            'whatsapp_numero' => $config['whatsapp_numero'] ?? '',
            'telefono' => $config['telefono'] ?? '',
            'direccion' => $config['direccion'] ?? '',
        ]);
    }

    public function update(Request $request)
    {
        $validado = $request->validate([
            'whatsapp_numero' => 'required|string|max:20|regex:/^[0-9]+$/',
            'telefono' => 'nullable|string|max:30',
            'direccion' => 'nullable|string|max:255',
        ], [
            'whatsapp_numero.regex' => 'El número de WhatsApp debe llevar solo números, con código de país y sin espacios ni símbolos (ej: 50312345678).',
        ]);

        foreach ($validado as $clave => $valor) {
            Configuracion::updateOrCreate(
                ['clave' => $clave],
                ['valor' => $valor ?? '']
            );
        }

        return redirect()
            ->route('admin.configuracion.edit')
            ->with('status', 'Configuración actualizada correctamente.');
    }
}
