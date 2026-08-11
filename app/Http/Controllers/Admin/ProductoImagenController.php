<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoImagenController extends Controller
{
    public function store(Request $request, Producto $producto)
    {
        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|max:2048',
        ]);

        $orden = ($producto->imagenes()->max('orden') ?? -1) + 1;

        foreach ($request->file('imagenes') as $archivo) {
            $ruta = $archivo->store('productos', 'public');

            $producto->imagenes()->create([
                'ruta' => $ruta,
                'orden' => $orden,
            ]);

            $orden++;
        }

        return back()->with('status', 'Imágenes agregadas correctamente.');
    }

    public function destroy(Producto $producto, ImagenProducto $imagen)
    {
        abort_unless($imagen->producto_id === $producto->id, 404);

        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        return back()->with('status', 'Imagen eliminada.');
    }
}