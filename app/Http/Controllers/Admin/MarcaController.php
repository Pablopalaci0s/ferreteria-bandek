<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarcaController extends Controller
{
    public function index(Request $request)
    {
        $marcas = Marca::withCount('productos')
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->string('buscar') . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.marcas.index', compact('marcas'));
    }

    public function create()
    {
        return view('admin.marcas.create');
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:150',
            'activo' => 'boolean',
        ]);

        $validado['slug'] = Str::slug($validado['nombre']);

        Marca::create($validado);

        return redirect()
            ->route('admin.marcas.index')
            ->with('status', 'Marca creada correctamente.');
    }

    public function edit(Marca $marca)
    {
        return view('admin.marcas.edit', compact('marca'));
    }

    public function update(Request $request, Marca $marca)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:150',
            'activo' => 'boolean',
        ]);

        $validado['slug'] = Str::slug($validado['nombre']);

        $marca->update($validado);

        return redirect()
            ->route('admin.marcas.index')
            ->with('status', 'Marca actualizada.');
    }

    public function destroy(Marca $marca)
    {
        $marca->delete();

        return redirect()
            ->route('admin.marcas.index')
            ->with('status', 'Marca eliminada.');
    }
}
