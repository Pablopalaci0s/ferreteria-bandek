<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ImagenOptimizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::when($request->filled('buscar'), function ($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->string('buscar') . '%');
            })
            ->orderBy('orden')
            ->orderByDesc('id')
            ->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validado = $this->validarDatos($request);

        $validado['imagen'] = ImagenOptimizada::guardar($request->file('imagen'), 'banners', 1920, 82);

        Banner::create($validado);

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner creado correctamente.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validado = $this->validarDatos($request, esCreacion: false);

        if ($request->hasFile('imagen')) {

            if ($banner->imagen) {
                Storage::disk('public')->delete($banner->imagen);
            }

            $validado['imagen'] = ImagenOptimizada::guardar($request->file('imagen'), 'banners', 1920, 82);
        }

        $banner->update($validado);

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner actualizado.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->imagen) {
            Storage::disk('public')->delete($banner->imagen);
        }

        $banner->delete();

        return redirect()
            ->route('admin.banners.index')
            ->with('status', 'Banner eliminado.');
    }

    private function validarDatos(Request $request, bool $esCreacion = true): array
    {
        return $request->validate([
            'titulo' => 'nullable|string|max:150',
            'link' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
            'imagen' => ($esCreacion ? 'required' : 'nullable') . '|image|max:4096',
        ]);
    }
}
