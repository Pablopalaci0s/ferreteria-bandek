<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ImagenOptimizada;
use App\Support\ReglaImagen;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $zona = $request->string('zona')->toString();

        if (! array_key_exists($zona, Banner::ZONAS)) {
            $zona = array_key_first(Banner::ZONAS);
        }

        $banners = Banner::when($request->filled('buscar'), function ($q) use ($request) {
            $q->where('titulo', 'like', '%'.$request->string('buscar').'%');
        })
            ->where('zona', $zona)
            ->orderBy('orden')
            ->orderByDesc('id')
            ->get();

        return view('admin.banners.index', [
            'banners' => $banners,
            'zona' => $zona,
            'zonas' => Banner::ZONAS,
        ]);
    }

    public function create(Request $request)
    {
        $zona = $request->string('zona')->toString();

        if (! array_key_exists($zona, Banner::ZONAS)) {
            $zona = array_key_first(Banner::ZONAS);
        }

        return view('admin.banners.create', ['zonaSeleccionada' => $zona]);
    }

    public function store(Request $request)
    {
        $validado = $this->validarDatos($request);

        $medidas = Banner::ZONAS[$validado['zona']];

        $validado['imagen'] = ImagenOptimizada::guardar(
            $request->file('imagen'),
            'banners',
            $medidas['ancho'],
            $medidas['alto'],
            82
        );

        Banner::create($validado);

        return redirect()
            ->route('admin.banners.index', ['zona' => $validado['zona']])
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

            ImagenOptimizada::eliminar($banner->imagen);

            $medidas = Banner::ZONAS[$validado['zona']];

            $validado['imagen'] = ImagenOptimizada::guardar(
                $request->file('imagen'),
                'banners',
                $medidas['ancho'],
                $medidas['alto'],
                82
            );
        }

        $banner->update($validado);

        return redirect()
            ->route('admin.banners.index', ['zona' => $validado['zona']])
            ->with('status', 'Banner actualizado.');
    }

    public function destroy(Banner $banner)
    {
        ImagenOptimizada::eliminar($banner->imagen);

        $zona = $banner->zona;

        $banner->delete();

        return redirect()
            ->route('admin.banners.index', ['zona' => $zona])
            ->with('status', 'Banner eliminado.');
    }

    private function validarDatos(Request $request, bool $esCreacion = true): array
    {
        return $request->validate([
            'titulo' => 'nullable|string|max:150',
            'zona' => 'required|in:'.implode(',', array_keys(Banner::ZONAS)),
            'link' => 'nullable|string|max:255',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
            'imagen' => ReglaImagen::reglas(requerida: $esCreacion),
        ]);
    }
}
