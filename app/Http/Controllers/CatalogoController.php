<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function inicio()
    {
        $banners = Banner::where('activo', true)
            ->orderBy('orden')
            ->get();

        $destacados = Producto::with('categoria')
            ->where('activo', true)
            ->where('destacado', true)
            ->latest()
            ->take(8)
            ->get();

        $categorias = Categoria::where('activo', true)->get();

        return view('catalogo.inicio', compact('banners', 'destacados', 'categorias'));
    }

    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'marca'])
            ->where('activo', true);

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('slug', $request->string('categoria')->toString());
            });
        }

        // Filtro por marca
        if ($request->filled('marca')) {
            $query->whereHas('marca', function ($q) use ($request) {
                $q->where('slug', $request->string('marca')->toString());
            });
        }

        // Búsqueda normal
        if ($request->filled('buscar')) {

            $buscar = $request->string('buscar')->toString();

            $query->where(function ($q) use ($buscar) {

                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%")
                    ->orWhere('modelo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('descripcion_larga', 'like', "%{$buscar}%")

                    ->orWhereHas('marca', function ($marca) use ($buscar) {
                        $marca->where('nombre', 'like', "%{$buscar}%");
                    })

                    ->orWhereHas('categoria', function ($categoria) use ($buscar) {
                        $categoria->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        $productos = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categorias = Categoria::where('activo', true)->get();

        $marcas = Marca::where('activo', true)->get();

        return view(
            'catalogo.index',
            compact('productos', 'categorias', 'marcas')
        );
    }

    public function buscar(Request $request)
    {
        $buscar = trim($request->input('q', ''));

        $query = Producto::with(['categoria', 'marca'])
            ->where('activo', true);

        // Búsqueda
        if ($buscar !== '') {

            $query->where(function ($q) use ($buscar) {

                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('sku', 'like', "%{$buscar}%")
                    ->orWhere('modelo', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%")
                    ->orWhere('descripcion_larga', 'like', "%{$buscar}%")

                    ->orWhereHas('marca', function ($marca) use ($buscar) {
                        $marca->where('nombre', 'like', "%{$buscar}%");
                    })

                    ->orWhereHas('categoria', function ($categoria) use ($buscar) {
                        $categoria->where('nombre', 'like', "%{$buscar}%");
                    });
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {

            $categoria = $request->input('categoria');

            $query->whereHas('categoria', function ($q) use ($categoria) {
                $q->where('slug', $categoria);
            });
        }

        // Filtro por marca
        if ($request->filled('marca')) {

            $marca = $request->input('marca');

            $query->whereHas('marca', function ($q) use ($marca) {
                $q->where('slug', $marca);
            });
        }

        $productos = $query
            ->latest()
            ->take(12)
            ->get();

        return response()->json([
            'productos' => $productos->map(function ($producto) {

                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'slug' => $producto->slug,
                    'sku' => $producto->sku,
                    'modelo' => $producto->modelo,

                    'precio' => number_format(
                        $producto->precio,
                        2
                    ),

                    'imagen' => $producto->imagen_principal
                        ? asset(
                            'storage/' .
                            $producto->imagen_principal
                        )
                        : 'https://placehold.co/500x500?text=BANDEK',

                    'categoria' => $producto->categoria?->nombre,
                    'marca' => $producto->marca?->nombre,

                    'url' => route(
                        'catalogo.show',
                        $producto
                    ),

                    'whatsapp' => $producto->whatsapp_link,
                ];
            }),
        ]);
    }

    public function show(Producto $producto)
    {
        abort_unless($producto->activo, 404);

        return view('catalogo.show', compact('producto'));
    }
}

