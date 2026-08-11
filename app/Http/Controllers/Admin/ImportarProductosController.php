<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImportarProductosController extends Controller
{
    /**
     * Columnas esperadas en el CSV (en este orden, pero se detectan por nombre
     * de encabezado, así que el orden de las columnas no importa).
     */
    private const COLUMNAS = [
        'sku', 'nombre', 'modelo', 'descripcion', 'descripcion_larga',
        'precio', 'precio_oferta', 'costo', 'stock', 'stock_minimo',
        'categoria', 'marca', 'proveedor', 'unidad_medida', 'activo', 'destacado',
    ];

    public function formulario()
    {
        return view('admin.productos.importar');
    }

    public function plantilla()
    {
        $encabezados = self::COLUMNAS;

        $filaEjemplo = [
            'HER-001', 'Taladro percutor 1/2" 550W', 'GSB 13 RE', 'Taladro percutor de uso profesional', '',
            '45.99', '', '30.00', '10', '2',
            'Herramientas', 'Truper', '', 'Unidad', 'si', 'no',
        ];

        $callback = function () use ($encabezados, $filaEjemplo) {
            $salida = fopen('php://output', 'w');

            // BOM para que Excel en Windows detecte UTF-8 correctamente
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, $encabezados);
            fputcsv($salida, $filaEjemplo);

            fclose($salida);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla-productos.csv"',
        ]);
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|max:5120',
        ]);

        $archivo = $request->file('archivo');

        $rutaTemporal = $archivo->getRealPath();
        $manejador = fopen($rutaTemporal, 'r');

        if (! $manejador) {
            return back()->with('error', 'No se pudo leer el archivo. Probá exportarlo de nuevo como CSV.');
        }

        // Quitar el BOM de UTF-8 si existe
        $primerosBytes = fread($manejador, 3);
        if ($primerosBytes !== "\xEF\xBB\xBF") {
            rewind($manejador);
        }

        $encabezadosCrudos = fgetcsv($manejador);

        if (! $encabezadosCrudos) {
            fclose($manejador);
            return back()->with('error', 'El archivo está vacío o no tiene encabezados.');
        }

        $mapaColumnas = $this->mapearEncabezados($encabezadosCrudos);

        if (! isset($mapaColumnas['sku']) || ! isset($mapaColumnas['nombre'])) {
            fclose($manejador);
            return back()->with('error', 'El archivo debe tener al menos las columnas "sku" y "nombre".');
        }

        $creados = 0;
        $actualizados = 0;
        $errores = [];
        $numeroFila = 1; // la fila 1 es el encabezado

        while (($fila = fgetcsv($manejador)) !== false) {

            $numeroFila++;

            // Saltar filas completamente vacías
            if (count(array_filter($fila, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $datos = [];
            foreach ($mapaColumnas as $campo => $indice) {
                $datos[$campo] = isset($fila[$indice]) ? trim((string) $fila[$indice]) : '';
            }

            try {
                $resultado = $this->guardarFila($datos);

                if ($resultado === 'creado') {
                    $creados++;
                } else {
                    $actualizados++;
                }

            } catch (\Throwable $e) {
                $errores[] = "Fila {$numeroFila} (SKU: " . ($datos['sku'] ?: '—') . '): ' . $e->getMessage();
            }
        }

        fclose($manejador);

        return back()->with('resultado_importacion', [
            'creados' => $creados,
            'actualizados' => $actualizados,
            'errores' => $errores,
        ]);
    }

    /**
     * Relaciona los encabezados del archivo (que pueden venir en cualquier orden,
     * con mayúsculas/tildes/espacios distintos) con los campos que esperamos.
     */
    private function mapearEncabezados(array $encabezadosCrudos): array
    {
        $mapa = [];

        foreach ($encabezadosCrudos as $indice => $encabezado) {

            $normalizado = $this->normalizar($encabezado);

            if (in_array($normalizado, self::COLUMNAS, true)) {
                $mapa[$normalizado] = $indice;
            }
        }

        return $mapa;
    }

    private function normalizar(string $texto): string
    {
        $texto = trim(mb_strtolower($texto));

        $texto = strtr($texto, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
        ]);

        $texto = preg_replace('/\s+/', '_', $texto);

        return $texto;
    }

    /**
     * Crea o actualiza (según el SKU) un producto a partir de una fila del CSV.
     * Devuelve 'creado' o 'actualizado'. Lanza una excepción con un mensaje
     * legible si la fila no es válida.
     */
    private function guardarFila(array $datos): string
    {
        $sku = $datos['sku'] ?? '';
        $nombre = $datos['nombre'] ?? '';

        if ($sku === '') {
            throw new \RuntimeException('falta el SKU.');
        }

        if ($nombre === '') {
            throw new \RuntimeException('falta el nombre.');
        }

        $precio = $this->numero($datos['precio'] ?? null);

        if ($precio === null) {
            throw new \RuntimeException('el precio no es válido.');
        }

        $categoriaNombre = $datos['categoria'] ?? '';

        if ($categoriaNombre === '') {
            throw new \RuntimeException('falta la categoría.');
        }

        $categoria = Categoria::firstOrCreate(
            ['nombre' => $categoriaNombre],
            ['slug' => Str::slug($categoriaNombre), 'activo' => true]
        );

        $unidadNombre = $datos['unidad_medida'] ?? '';

        if ($unidadNombre === '') {
            throw new \RuntimeException('falta la unidad de medida.');
        }

        $unidad = UnidadMedida::where('nombre', $unidadNombre)
            ->orWhere('abreviatura', $unidadNombre)
            ->first();

        if (! $unidad) {
            throw new \RuntimeException("la unidad de medida \"{$unidadNombre}\" no existe (creala primero en Productos > Unidades).");
        }

        $marcaId = null;
        if (! empty($datos['marca'])) {
            $marca = Marca::firstOrCreate(
                ['nombre' => $datos['marca']],
                ['slug' => Str::slug($datos['marca']), 'activo' => true]
            );
            $marcaId = $marca->id;
        }

        $proveedorId = null;
        if (! empty($datos['proveedor'])) {
            $proveedor = Proveedor::firstOrCreate(
                ['nombre' => $datos['proveedor']],
                ['activo' => true]
            );
            $proveedorId = $proveedor->id;
        }

        $precioOferta = $this->numero($datos['precio_oferta'] ?? null);

        if ($precioOferta !== null && $precioOferta >= $precio) {
            // Precio de oferta inválido: lo ignoramos en vez de rechazar toda la fila
            $precioOferta = null;
        }

        $valores = [
            'nombre' => $nombre,
            'modelo' => $datos['modelo'] ?: null,
            'descripcion' => $datos['descripcion'] ?: null,
            'descripcion_larga' => $datos['descripcion_larga'] ?: null,
            'precio' => $precio,
            'precio_oferta' => $precioOferta,
            'costo' => $this->numero($datos['costo'] ?? null),
            'stock_minimo' => (int) ($datos['stock_minimo'] ?: 0),
            'categoria_id' => $categoria->id,
            'marca_id' => $marcaId,
            'proveedor_id' => $proveedorId,
            'unidad_medida_id' => $unidad->id,
            'activo' => $this->booleano($datos['activo'] ?? '', true),
            'destacado' => $this->booleano($datos['destacado'] ?? '', false),
        ];

        $existente = Producto::where('sku', $sku)->first();

        if ($existente) {
            $existente->update($valores);
            return 'actualizado';
        }

        $valores['sku'] = $sku;
        $valores['slug'] = Str::slug($nombre) . '-' . uniqid();
        $valores['stock'] = (int) ($datos['stock'] ?: 0);

        Producto::create($valores);

        return 'creado';
    }

    private function numero(?string $valor): ?float
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $limpio = str_replace(['$', ',', ' '], '', $valor);

        return is_numeric($limpio) ? (float) $limpio : null;
    }

    private function booleano(string $valor, bool $porDefecto): bool
    {
        $valor = mb_strtolower(trim($valor));

        if ($valor === '') {
            return $porDefecto;
        }

        return in_array($valor, ['1', 'si', 'sí', 'true', 'x', 'yes'], true);
    }
}
