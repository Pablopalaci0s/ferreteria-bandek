<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImagenProducto;
use App\Models\Producto;
use App\Support\ImagenOptimizada;
use App\Support\ReglaImagen;
use Illuminate\Http\Request;

class ProductoImagenController extends Controller
{
    public function store(Request $request, Producto $producto)
    {
        $request->validate([
            'imagenes' => 'required|array|max:12',
            'imagenes.*' => ReglaImagen::reglas(requerida: false),
        ]);

        $orden = ($producto->imagenes()->max('orden') ?? -1) + 1;

        foreach ($request->file('imagenes') as $archivo) {
            $ruta = ImagenOptimizada::guardar($archivo, 'productos', 1400, 1400, 82, thumbnail: 500);

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

        ImagenOptimizada::eliminar($imagen->ruta);
        $imagen->delete();

        return back()->with('status', 'Imagen eliminada.');
    }
}
