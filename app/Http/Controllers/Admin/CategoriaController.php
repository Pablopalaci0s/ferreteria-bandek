<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::withCount('productos')
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->string('buscar') . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        $categoriasPadre = Categoria::whereNull('categoria_padre_id')->get();

        return view('admin.categorias.create', compact('categoriasPadre'));
    }

    public function store(Request $request)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_padre_id' => 'nullable|exists:categorias,id',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $validado['slug'] = Str::slug($validado['nombre']);

        if ($request->hasFile('imagen')) {
            $validado['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        Categoria::create($validado);

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        $categoriasPadre = Categoria::whereNull('categoria_padre_id')
            ->where('id', '!=', $categoria->id)
            ->get();

        return view(
            'admin.categorias.edit',
            compact('categoria', 'categoriasPadre')
        );
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validado = $request->validate([
            'nombre' => 'required|string|max:150',
            'categoria_padre_id' => 'nullable|exists:categorias,id',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $validado['slug'] = Str::slug($validado['nombre']);

        if ($request->hasFile('imagen')) {

            if ($categoria->imagen) {
                Storage::disk('public')->delete($categoria->imagen);
            }

            $validado['imagen'] = $request->file('imagen')->store('categorias', 'public');
        }

        $categoria->update($validado);

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->imagen) {
            Storage::disk('public')->delete($categoria->imagen);
        }

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('status', 'Categoría eliminada.');
    }
}