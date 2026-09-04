<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogoController extends Controller
{
    public function inicio()
    {
        $bannersPorZona = Banner::where('activo', true)
            ->orderBy('orden')
            ->get()
            ->groupBy('zona');

        $bannersPrincipal = $bannersPorZona->get('hero_principal', collect());
        $bannersSecundario = $bannersPorZona->get('hero_secundario', collect());
        $bannersPromocion = $bannersPorZona->get('promocion', collect());
        $bannersFranja = $bannersPorZona->get('franja', collect());

        $destacados = Producto::with('categoria')
            ->where('activo', true)
            ->where('destacado', true)
            ->latest()
            ->take(8)
            ->get();

        $categorias = Categoria::whereNull('categoria_padre_id')->where('activo', true)->get();

        $marcas = Marca::where('activo', true)->orderBy('nombre')->get();

        $totalProductos = Producto::where('activo', true)->count();

        return view('catalogo.inicio', compact(
            'bannersPrincipal',
            'bannersSecundario',
            'bannersPromocion',
            'bannersFranja',
            'destacados',
            'categorias',
            'marcas',
            'totalProductos'
        ));
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
        $buscar = $request->string('buscar')->toString();

        if ($buscar !== '') {
            $query->buscar($buscar);
        }

        // Solo disponibles
        if ($request->boolean('disponible')) {
            $query->where('stock', '>', 0);
        }

        // Solo en oferta
        if ($request->boolean('oferta')) {
            $query->whereNotNull('precio_oferta')->whereColumn('precio_oferta', '<', 'precio');
        }

        $productos = $this->aplicarOrden($query, $request->string('orden')->toString())
            ->paginate(12)
            ->withQueryString();

        $sugerencia = $buscar !== '' && $productos->total() === 0
            ? Producto::sugerirCorreccion($buscar)
            : null;

        $categorias = Categoria::where('activo', true)->get();

        $marcas = Marca::where('activo', true)->get();

        return view(
            'catalogo.index',
            compact('productos', 'categorias', 'marcas', 'sugerencia')
        );
    }

    public function buscar(Request $request)
    {
        $buscar = trim($request->input('q', ''));

        $query = Producto::with(['categoria', 'marca'])
            ->where('activo', true);

        // Búsqueda
        if ($buscar !== '') {
            $query->buscar($buscar);
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

        // Solo disponibles
        if ($request->boolean('disponible')) {
            $query->where('stock', '>', 0);
        }

        // Solo en oferta
        if ($request->boolean('oferta')) {
            $query->whereNotNull('precio_oferta')->whereColumn('precio_oferta', '<', 'precio');
        }

        $productos = $this->aplicarOrden($query, $request->input('orden'))
            ->take(12)
            ->get();

        $sugerencia = $buscar !== '' && $productos->isEmpty()
            ? Producto::sugerirCorreccion($buscar)
            : null;

        return response()->json([
            'sugerencia' => $sugerencia,
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

                    'en_oferta' => $producto->en_oferta,

                    'precio_oferta' => $producto->en_oferta
                        ? number_format($producto->precio_oferta, 2)
                        : null,

                    'stock' => $producto->stock,

                    'imagen' => $producto->imagen_principal
                        ? Storage::disk('public')->url($producto->imagen_thumb)
                        : asset('img/logo-completo.png'),

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

        $relacionados = Producto::with('categoria')
            ->where('activo', true)
            ->where('id', '!=', $producto->id)
            ->where('categoria_id', $producto->categoria_id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Si no hay suficientes de la misma categoría, completamos con la misma marca
        if ($relacionados->count() < 4 && $producto->marca_id) {

            $faltan = 4 - $relacionados->count();

            $extra = Producto::where('activo', true)
                ->where('id', '!=', $producto->id)
                ->whereNotIn('id', $relacionados->pluck('id'))
                ->where('marca_id', $producto->marca_id)
                ->inRandomOrder()
                ->take($faltan)
                ->get();

            $relacionados = $relacionados->merge($extra);
        }

        return view('catalogo.show', compact('producto', 'relacionados'));
    }

    /**
     * Aplica el orden pedido a la consulta. Por defecto, los más recientes primero.
     */
    private function aplicarOrden($query, ?string $orden)
    {
        return match ($orden) {
            'precio_asc' => $query->orderBy('precio', 'asc'),
            'precio_desc' => $query->orderBy('precio', 'desc'),
            'nombre_asc' => $query->orderBy('nombre', 'asc'),
            default => $query->latest(),
        };
    }
}
