<?php

namespace App\Models;

use App\Support\ImagenOptimizada;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'modelo',
        'nombre',
        'slug',
        'descripcion',
        'descripcion_larga',
        'precio',
        'precio_oferta',
        'costo',
        'stock',
        'stock_minimo',
        'categoria_id',
        'marca_id',
        'proveedor_id',
        'unidad_medida_id',
        'imagen_principal',
        'peso',
        'activo',
        'destacado',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'precio' => 'decimal:2',
        'precio_oferta' => 'decimal:2',
    ];

    public function getEnOfertaAttribute(): bool
    {
        return ! is_null($this->precio_oferta) && $this->precio_oferta < $this->precio;
    }

    public function getPrecioFinalAttribute()
    {
        return $this->en_oferta ? $this->precio_oferta : $this->precio;
    }

    /**
     * Ruta del thumbnail de la imagen principal (cae a la imagen completa
     * si el thumbnail no existe, p. ej. productos cargados antes del cambio).
     */
    public function getImagenThumbAttribute(): ?string
    {
        return ImagenOptimizada::thumb($this->imagen_principal);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class)->orderBy('orden');
    }

    public function solicitudesPrecio()
    {
        return $this->hasMany(SolicitudPrecio::class);
    }

    public function solicitudPrecioPendiente()
    {
        return $this->solicitudesPrecio()->pendientes()->latest()->first();
    }

    public function getWhatsappLinkAttribute()
    {
        $numero = Configuracion::where('clave', 'whatsapp_numero')->value('valor');
        $mensaje = "Hola, quiero pedir: {$this->nombre} - \${$this->precio_final}";

        return "https://wa.me/{$numero}?text=".urlencode($mensaje);
    }

    /**
     * Busca por nombre/SKU/modelo/descripcion/marca/categoria, sin importar
     * mayusculas o minusculas. En Postgres LIKE distingue mayusculas de
     * minusculas (a diferencia de MySQL, donde no); comparar todo en
     * minuscula evita que una busqueda en minuscula deje de encontrar
     * productos guardados en mayuscula (como los que vienen del CSV de
     * facturacion).
     */
    public function scopeBuscar($query, string $termino)
    {
        $like = '%'.mb_strtolower($termino).'%';

        return $query->where(function ($q) use ($like) {
            $q->whereRaw('LOWER(nombre) LIKE ?', [$like])
                ->orWhereRaw('LOWER(sku) LIKE ?', [$like])
                ->orWhereRaw('LOWER(modelo) LIKE ?', [$like])
                ->orWhereRaw('LOWER(descripcion) LIKE ?', [$like])
                ->orWhereRaw('LOWER(descripcion_larga) LIKE ?', [$like])
                ->orWhereHas('marca', fn ($marca) => $marca->whereRaw('LOWER(nombre) LIKE ?', [$like]))
                ->orWhereHas('categoria', fn ($categoria) => $categoria->whereRaw('LOWER(nombre) LIKE ?', [$like]));
        });
    }

    /**
     * Si una busqueda no encuentra nada, sugiere la palabra mas parecida
     * (nombre de producto, marca o categoria) por si fue un error de
     * tipeo. Sencillo a proposito (distancia de Levenshtein contra el
     * catalogo activo) - alcanza sobra para un catalogo de este tamano.
     */
    public static function sugerirCorreccion(string $termino): ?string
    {
        $termino = trim(mb_strtolower($termino));

        if ($termino === '') {
            return null;
        }

        $vocabulario = Cache::remember('catalogo.vocabulario_busqueda', now()->addHour(), function () {
            $palabras = collect();

            static::where('activo', true)->pluck('nombre')->each(function ($nombre) use ($palabras) {
                foreach (preg_split('/\s+/u', $nombre) as $palabra) {
                    $palabra = mb_strtolower(trim($palabra, ".,:;()#`\"'"));

                    if (mb_strlen($palabra) >= 3) {
                        $palabras->push($palabra);
                    }
                }
            });

            Marca::pluck('nombre')->each(fn ($nombre) => $palabras->push(mb_strtolower($nombre)));
            Categoria::pluck('nombre')->each(fn ($nombre) => $palabras->push(mb_strtolower($nombre)));

            return $palabras->unique()->values();
        });

        $mejor = null;
        $mejorDistancia = null;

        foreach ($vocabulario as $palabra) {
            $distancia = levenshtein($termino, $palabra);
            $umbral = max(2, (int) ceil(mb_strlen($termino) / 3));

            if ($distancia > 0 && $distancia <= $umbral && ($mejorDistancia === null || $distancia < $mejorDistancia)) {
                $mejor = $palabra;
                $mejorDistancia = $distancia;
            }
        }

        return $mejor;
    }

    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    /**
     * Registra un movimiento de inventario y ajusta el stock del producto,
     * todo dentro de una transacción para que ambas cosas ocurran juntas o
     * ninguna. Si se llama dentro de otra transacción (p. ej. al confirmar
     * una venta), participa como savepoint de esa transacción externa.
     *
     * Tipos y su efecto sobre el stock:
     *   entrada, devolucion  -> suma
     *   venta, salida, perdida -> resta
     *   ajuste               -> fija el stock al valor indicado
     */
    public function registrarMovimiento(
        string $tipo,
        int $cantidad,
        ?string $motivo,
        ?int $usuarioId,
        ?int $ventaId = null
    ): MovimientoInventario {
        return DB::transaction(function () use ($tipo, $cantidad, $motivo, $usuarioId, $ventaId) {
            $movimiento = MovimientoInventario::create([
                'producto_id' => $this->id,
                'usuario_id' => $usuarioId,
                'venta_id' => $ventaId,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'motivo' => $motivo,
            ]);

            $this->stock = match ($tipo) {
                'entrada', 'devolucion' => $this->stock + $cantidad,
                'venta', 'salida', 'perdida' => $this->stock - $cantidad,
                'ajuste' => $cantidad,
            };

            $this->save();

            return $movimiento;
        });
    }
}
