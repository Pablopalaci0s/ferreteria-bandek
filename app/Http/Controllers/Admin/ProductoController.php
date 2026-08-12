<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SolicitudPrecio;
use App\Models\UnidadMedida;
use App\Support\ImagenOptimizada;
use App\Support\ReglaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $productos = Producto::with(['categoria', 'marca'])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->string('buscar')->toString();

                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('sku', 'like', "%{$buscar}%")
                        ->orWhere('modelo', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.productos.index', compact('productos'));
    }

    public function create()
    {
        return view('admin.productos.create', $this->datosFormulario());
    }

    public function store(Request $request)
    {
        $validado = $this->validarDatos($request);

        $validado['slug'] = Str::slug($validado['nombre']).'-'.uniqid();

        if ($request->hasFile('imagen_principal')) {
            $validado['imagen_principal'] =
                ImagenOptimizada::guardar($request->file('imagen_principal'), 'productos', 1400, 1400, 82, thumbnail: 500);
        }

        Producto::create($validado);

        return redirect()
            ->route('admin.productos.index')
            ->with('status', 'Producto creado correctamente.');
    }

    public function edit(Producto $producto)
    {
        return view(
            'admin.productos.edit',
            array_merge(
                ['producto' => $producto],
                $this->datosFormulario()
            )
        );
    }

    public function update(Request $request, Producto $producto)
    {
        $validado = $this->validarDatos($request);

        if ($request->hasFile('imagen_principal')) {

            ImagenOptimizada::eliminar($producto->imagen_principal);

            $validado['imagen_principal'] =
                ImagenOptimizada::guardar($request->file('imagen_principal'), 'productos', 1400, 1400, 82, thumbnail: 500);
        }

        $usuario = $request->user();

        if ($usuario->rol === 'vendedor' && $this->cambioDePrecio($producto, $validado)) {

            $this->registrarSolicitudPrecio($producto, $validado, $usuario);

            // El precio no se toca todavía: queda a la espera de aprobación.
            unset($validado['precio'], $validado['precio_oferta']);

            $producto->update($validado);

            return redirect()
                ->route('admin.productos.index')
                ->with('status', 'Se guardaron los cambios del producto. El cambio de precio quedó pendiente de aprobación de un administrador.');
        }

        $producto->update($validado);

        return redirect()
            ->route('admin.productos.index')
            ->with('status', 'Producto actualizado.');
    }

    /**
     * Determina si el precio o precio de oferta enviados en el formulario
     * son distintos a los que el producto tiene guardados actualmente.
     */
    private function cambioDePrecio(Producto $producto, array $validado): bool
    {
        $precioActual = (float) $producto->precio;
        $precioNuevo = (float) $validado['precio'];

        $precioOfertaActual = is_null($producto->precio_oferta) ? null : (float) $producto->precio_oferta;
        $precioOfertaNuevo = is_null($validado['precio_oferta'] ?? null) ? null : (float) $validado['precio_oferta'];

        return $precioNuevo !== $precioActual || $precioOfertaNuevo !== $precioOfertaActual;
    }

    /**
     * Crea (o actualiza, si ya había una) la solicitud de cambio de precio
     * pendiente de revisión por un administrador.
     */
    private function registrarSolicitudPrecio(Producto $producto, array $validado, $usuario): void
    {
        $datos = [
            'producto_id' => $producto->id,
            'usuario_id' => $usuario->id,
            'precio_actual' => (float) $producto->precio,
            'precio_nuevo' => (float) $validado['precio'],
            'precio_oferta_actual' => is_null($producto->precio_oferta) ? null : (float) $producto->precio_oferta,
            'precio_oferta_nuevo' => is_null($validado['precio_oferta'] ?? null) ? null : (float) $validado['precio_oferta'],
            'estado' => 'pendiente',
        ];

        $pendiente = $producto->solicitudPrecioPendiente();

        if ($pendiente) {
            $pendiente->update($datos);
        } else {
            SolicitudPrecio::create($datos);
        }
    }

    public function destroy(Producto $producto)
    {
        ImagenOptimizada::eliminar($producto->imagen_principal);

        foreach ($producto->imagenes as $imagen) {
            ImagenOptimizada::eliminar($imagen->ruta);
        }

        $producto->delete();

        return redirect()
            ->route('admin.productos.index')
            ->with('status', 'Producto eliminado.');
    }

    private function datosFormulario(): array
    {
        return [
            'categorias' => Categoria::where('activo', true)->get(),
            'marcas' => Marca::where('activo', true)->get(),
            'proveedores' => Proveedor::where('activo', true)->get(),
            'unidadesMedida' => UnidadMedida::all(),
        ];
    }

    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'sku' => 'required|string|max:50',
            'modelo' => 'nullable|string|max:255',
            'nombre' => 'required|string|max:255',

            'descripcion' => 'nullable|string',
            'descripcion_larga' => 'nullable|string',

            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'costo' => 'nullable|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',

            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',

            'imagen_principal' => ReglaImagen::reglas(requerida: false),

            'activo' => 'boolean',
            'destacado' => 'boolean',
        ]);
    }
}
