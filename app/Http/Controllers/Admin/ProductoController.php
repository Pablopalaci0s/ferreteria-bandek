<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $validado['slug'] = Str::slug($validado['nombre']) . '-' . uniqid();

        if ($request->hasFile('imagen_principal')) {
            $validado['imagen_principal'] =
                $request->file('imagen_principal')->store('productos', 'public');
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

            if ($producto->imagen_principal) {
                Storage::disk('public')
                    ->delete($producto->imagen_principal);
            }

            $validado['imagen_principal'] =
                $request->file('imagen_principal')->store('productos', 'public');
        }

        $producto->update($validado);

        return redirect()
            ->route('admin.productos.index')
            ->with('status', 'Producto actualizado.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen_principal) {
            Storage::disk('public')
                ->delete($producto->imagen_principal);
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

            'imagen_principal' => 'nullable|image|max:2048',

            'activo' => 'boolean',
            'destacado' => 'boolean',
        ]);
    }
}

