<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        $urls[] = [
            'loc' => route('inicio'),
            'lastmod' => now()->toAtomString(),
            'priority' => '1.0',
        ];

        $urls[] = [
            'loc' => route('catalogo.index'),
            'lastmod' => now()->toAtomString(),
            'priority' => '0.9',
        ];

        $urls[] = [
            'loc' => route('nosotros'),
            'lastmod' => now()->toAtomString(),
            'priority' => '0.5',
        ];

        foreach (Categoria::where('activo', true)->get() as $categoria) {
            $urls[] = [
                'loc' => route('catalogo.index', ['categoria' => $categoria->slug]),
                'lastmod' => $categoria->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.7',
            ];
        }

        foreach (Producto::where('activo', true)->get() as $producto) {
            $urls[] = [
                'loc' => route('catalogo.show', $producto),
                'lastmod' => $producto->updated_at?->toAtomString() ?? now()->toAtomString(),
                'priority' => '0.8',
            ];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
