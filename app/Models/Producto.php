<?php

namespace App\Models;

use App\Support\ImagenOptimizada;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
