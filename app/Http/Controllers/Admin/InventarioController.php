<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $movimientos = MovimientoInventario::with(['producto', 'usuario'])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->string('buscar')->toString();

                $q->where('motivo', 'like', "%{$buscar}%")
                    ->orWhereHas('producto', function ($p) use ($buscar) {
                        $p->where('nombre', 'like', "%{$buscar}%")
                            ->orWhere('sku', 'like', "%{$buscar}%");
                    });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inventario.index', compact('movimientos'));
    }

    public function create()
    {
        $productos = Producto::orderBy('nombre')->get();

        return view('admin.inventario.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            // Las salidas por venta las genera el módulo de Ventas, no acá.
            'tipo' => 'required|in:entrada,ajuste,devolucion,perdida',
            'cantidad' => 'required|integer|min:0',
            'motivo' => 'nullable|string|max:255',
        ]);

        $producto = Producto::findOrFail($validado['producto_id']);

        if ($validado['tipo'] === 'perdida' && $validado['cantidad'] > $producto->stock) {
            return back()->withErrors(['cantidad' => 'No podés registrar una pérdida mayor al stock actual ('.$producto->stock.').'])->withInput();
        }

        $producto->registrarMovimiento(
            $validado['tipo'],
            $validado['cantidad'],
            $validado['motivo'],
            auth()->id()
        );

        return redirect()->route('admin.inventario.index')->with('status', 'Movimiento registrado. Nuevo stock de '.$producto->nombre.': '.$producto->stock);
    }
}
