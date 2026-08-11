<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedor::withCount('productos')
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->string('buscar')->toString();

                $q->where(function ($sub) use ($buscar) {
                    $sub->where('nombre', 'like', "%{$buscar}%")
                        ->orWhere('contacto_nombre', 'like', "%{$buscar}%")
                        ->orWhere('telefono', 'like', "%{$buscar}%")
                        ->orWhere('email', 'like', "%{$buscar}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('admin.proveedores.create');
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:200',
            'contacto_nombre' => 'nullable|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        Proveedor::create($validado);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('status', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('admin.proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:200',
            'contacto_nombre' => 'nullable|string|max:150',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        $proveedor->update($validado);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('status', 'Proveedor actualizado.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('status', 'Proveedor eliminado.');
    }
}
