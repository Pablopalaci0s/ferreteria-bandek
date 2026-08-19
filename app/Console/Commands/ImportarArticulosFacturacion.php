<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Importa el CSV exportado del sistema de facturación electrónica
 * (columnas: codigo_arti, nomb_arti, desc_marc, nomb_medi, prec_prod_medi,
 * prec_prod_mayo, desc_cate_arti, exmi_arti, compute_0009, codi_medi,
 * codi_cate_arti, coun_arti).
 *
 * Decisiones de negocio ya acordadas con el dueño de la tienda:
 * - El archivo no trae existencia real, solo un mínimo de reorden
 *   (exmi_arti) que no usamos como stock. Todo se carga con stock=50.
 * - El precio de mayoreo (prec_prod_mayo) NO se usa como precio_oferta
 *   público; solo se importa el precio normal (prec_prod_medi).
 * - Un mismo código puede repetirse con distinta unidad de medida
 *   (ej. cable por metro y por rollo). Cada combinación código+unidad
 *   se carga como un producto separado.
 */
class ImportarArticulosFacturacion extends Command
{
    protected $signature = 'importar:articulos-facturacion
        {archivo : Ruta al CSV exportado del sistema de facturación}
        {--dry-run : Solo muestra el resumen, no guarda nada}';

    protected $description = 'Importa el catálogo de productos desde el CSV del sistema de facturación electrónica';

    /** Traduce el texto de categoría del CSV a un nombre de categoría canónico. */
    private const MAPA_CATEGORIAS = [
        'ELECTRICO' => 'Eléctrico',
        'FONTANERÍA' => 'Fontanería',
        'GRIFERÍA' => 'Grifería',
        'HERRAMIENTAS' => 'Herramientas',
        'LUMINARIA' => 'Luminaria',
        'MISCELÁNEA' => 'Miscelánea',
        'PINTURA' => 'Pintura',
        'SOLVENTE' => 'Solvente',
    ];

    /** Traduce la unidad de medida del CSV al nombre ya existente en unidades_medida. */
    private const MAPA_UNIDADES = [
        'UNIDAD' => 'Unidad',
        'METRO' => 'Metro',
        'LIBRA' => 'Libra',
        'GALÓN' => 'Galón',
        'ROLLO' => 'Rollo',
        'BLISTER' => 'Blister',
        'BOTELLA' => 'Botella',
        '1/4 GALÓN' => 'Cuarto de Galon',
        '1/2 GALÓN' => 'Medio Galon',
    ];

    /** Valores de marca que en realidad significan "sin marca". */
    private const SIN_MARCA = ['OTROS', 'SIN MARCA'];

    /** Valores centinela usados en el sistema origen para "costo no definido". */
    private const COSTO_INVALIDO = [777.0, 7777.0, 77777.0];

    public function handle(): int
    {
        $ruta = $this->argument('archivo');

        if (! is_readable($ruta)) {
            $this->error("No puedo leer el archivo: {$ruta}");

            return self::FAILURE;
        }

        $filas = $this->leerCsv($ruta);

        if (empty($filas)) {
            $this->error('El archivo no tiene filas de datos.');

            return self::FAILURE;
        }

        // Agrupar por código+unidad para descartar filas duplicadas exactas,
        // y detectar qué códigos tienen más de una unidad (necesitan sufijo).
        $porCodigoUnidad = [];
        $unidadesPorCodigo = [];

        foreach ($filas as $fila) {
            $clave = $fila['codigo'].'|'.$fila['unidad_csv'];

            if (! isset($porCodigoUnidad[$clave])) {
                $porCodigoUnidad[$clave] = $fila;
                $unidadesPorCodigo[$fila['codigo']][$fila['unidad_csv']] = true;
            }
        }

        $filasUnicas = array_values($porCodigoUnidad);
        $descartadasPorDuplicado = count($filas) - count($filasUnicas);

        $preparadas = [];
        $omitidas = [];

        foreach ($filasUnicas as $fila) {
            $esMultiUnidad = count($unidadesPorCodigo[$fila['codigo']]) > 1;

            $precio = $this->numero($fila['precio']);

            if ($precio === null || $precio <= 0) {
                $omitidas[] = "{$fila['codigo']} ({$fila['nombre']}): precio inválido.";

                continue;
            }

            $categoriaNombre = self::MAPA_CATEGORIAS[$fila['categoria_csv']] ?? null;

            if ($categoriaNombre === null) {
                $omitidas[] = "{$fila['codigo']} ({$fila['nombre']}): categoría desconocida \"{$fila['categoria_csv']}\".";

                continue;
            }

            $unidadNombre = self::MAPA_UNIDADES[$fila['unidad_csv']] ?? null;

            if ($unidadNombre === null) {
                $omitidas[] = "{$fila['codigo']} ({$fila['nombre']}): unidad desconocida \"{$fila['unidad_csv']}\".";

                continue;
            }

            $sku = $fila['codigo'];
            $nombre = $fila['nombre'];

            if ($esMultiUnidad) {
                $sufijo = Str::slug($unidadNombre, '');
                $sku = $fila['codigo'].'-'.mb_strtoupper($sufijo);
                $nombre = "{$fila['nombre']} ({$unidadNombre})";
            }

            $marcaNombre = in_array(mb_strtoupper(trim($fila['marca_csv'])), self::SIN_MARCA, true)
                ? null
                : trim($fila['marca_csv']);

            $costo = $this->numero($fila['costo']);

            if ($costo !== null && in_array($costo, self::COSTO_INVALIDO, true)) {
                $costo = null;
            }

            $stockMinimo = $this->numero($fila['stock_minimo']);

            $preparadas[] = [
                'sku' => $sku,
                'nombre' => $nombre,
                'precio' => $precio,
                'costo' => $costo,
                'stock_minimo' => $stockMinimo !== null ? (int) round($stockMinimo) : 0,
                'categoria_nombre' => $categoriaNombre,
                'marca_nombre' => $marcaNombre,
                'unidad_nombre' => $unidadNombre,
            ];
        }

        $this->info('Filas en el archivo: '.count($filas));
        $this->info("Descartadas por ser duplicado exacto (mismo código y unidad): {$descartadasPorDuplicado}");
        $this->info('Productos a importar: '.count($preparadas));
        $this->info('Omitidas por datos inválidos: '.count($omitidas));

        foreach ($omitidas as $motivo) {
            $this->warn('  - '.$motivo);
        }

        $categoriasNuevas = collect($preparadas)->pluck('categoria_nombre')->unique()
            ->diff(Categoria::pluck('nombre'));

        $marcasExistentesMin = Marca::pluck('nombre')->map(fn ($n) => mb_strtolower($n));

        $marcasNuevas = collect($preparadas)->pluck('marca_nombre')->filter()->unique()
            ->reject(fn ($n) => $marcasExistentesMin->contains(mb_strtolower($n)));

        $this->info('Categorías nuevas a crear: '.$categoriasNuevas->implode(', '));
        $this->info('Marcas nuevas a crear ('.$marcasNuevas->count().'): '.$marcasNuevas->sort()->implode(', '));

        if ($this->option('dry-run')) {
            $this->info('Dry-run: no se guardó nada.');

            return self::SUCCESS;
        }

        $creados = 0;

        DB::transaction(function () use ($preparadas, &$creados) {

            $categoriaCache = [];
            $marcaCache = [];
            $unidadCache = [];

            foreach ($preparadas as $datos) {

                $categoriaNombre = $datos['categoria_nombre'];
                if (! isset($categoriaCache[$categoriaNombre])) {
                    $categoriaCache[$categoriaNombre] = Categoria::firstOrCreate(
                        ['nombre' => $categoriaNombre],
                        ['slug' => Str::slug($categoriaNombre), 'activo' => true]
                    )->id;
                }

                $marcaId = null;
                if ($datos['marca_nombre'] !== null) {
                    $claveMarca = mb_strtolower($datos['marca_nombre']);

                    if (! isset($marcaCache[$claveMarca])) {
                        $existente = Marca::whereRaw('LOWER(nombre) = ?', [$claveMarca])->first();

                        $marcaCache[$claveMarca] = $existente
                            ? $existente->id
                            : Marca::create([
                                'nombre' => $datos['marca_nombre'],
                                'slug' => Str::slug($datos['marca_nombre']),
                                'activo' => true,
                            ])->id;
                    }
                    $marcaId = $marcaCache[$claveMarca];
                }

                $unidadNombre = $datos['unidad_nombre'];
                if (! isset($unidadCache[$unidadNombre])) {
                    $unidadCache[$unidadNombre] = UnidadMedida::where('nombre', $unidadNombre)->firstOrFail()->id;
                }

                Producto::create([
                    'sku' => $datos['sku'],
                    'nombre' => $datos['nombre'],
                    'slug' => Str::slug($datos['nombre']).'-'.uniqid(),
                    'precio' => $datos['precio'],
                    'precio_oferta' => null,
                    'costo' => $datos['costo'],
                    'stock' => 50,
                    'stock_minimo' => $datos['stock_minimo'],
                    'categoria_id' => $categoriaCache[$categoriaNombre],
                    'marca_id' => $marcaId,
                    'unidad_medida_id' => $unidadCache[$unidadNombre],
                    'activo' => true,
                    'destacado' => false,
                ]);

                $creados++;
            }
        });

        $this->info("Listo: {$creados} productos creados.");

        return self::SUCCESS;
    }

    /**
     * Lee el CSV (codificado en ISO-8859-1) respetando comillas/comas embebidas,
     * y devuelve un array asociativo por fila ya con la codificación arreglada.
     */
    private function leerCsv(string $ruta): array
    {
        $manejador = fopen($ruta, 'r');

        if (! $manejador) {
            return [];
        }

        // Saltar encabezado.
        fgetcsv($manejador, 0, ',', '"', '');

        $filas = [];

        while (($cruda = fgetcsv($manejador, 0, ',', '"', '')) !== false) {

            if (count($cruda) < 8 || trim((string) ($cruda[0] ?? '')) === '') {
                continue;
            }

            $campo = function (int $i) use ($cruda): string {
                if (! isset($cruda[$i])) {
                    return '';
                }

                $texto = trim(mb_convert_encoding((string) $cruda[$i], 'UTF-8', 'ISO-8859-1'));

                // El español no usa acento grave ni el acento suelto "´": son
                // errores de digitación/exportación del sistema origen por una
                // mala interpretación de codificación. "À" siempre debería ser
                // "Á", y "´" se usó como sustituto de un apóstrofe real.
                return strtr($texto, ['À' => 'Á', 'à' => 'á', '´' => "'"]);
            };

            $nombre = $campo(1);

            // Arregla las 4 filas de fontanería cuyo nombre empieza con "TEE"
            // entre comillas: el CSV mal formado hace que str_getcsv() se
            // coma esas comillas al interpretarlas como delimitador.
            if (str_starts_with($nombre, 'TEE ')) {
                $nombre = '"TEE"'.substr($nombre, 3);
            }

            $filas[] = [
                'codigo' => $campo(0),
                'nombre' => $nombre,
                'marca_csv' => $campo(2),
                'unidad_csv' => mb_strtoupper($campo(3)),
                'precio' => $campo(4),
                'precio_mayoreo' => $campo(5),
                'categoria_csv' => mb_strtoupper($campo(6)),
                'stock_minimo' => $campo(7),
                'costo' => $campo(11),
            ];
        }

        fclose($manejador);

        return $filas;
    }

    private function numero(?string $valor): ?float
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        return is_numeric($valor) ? (float) $valor : null;
    }
}
