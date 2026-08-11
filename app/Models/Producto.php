<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MovimientoInventario;

class Producto extends Model
{
    protected $fillable = [
        'sku',
        'modelo',
        'nombre',
        'slug',
        'descripcion',
        'descripcion_larga',
        'precio',
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
    ];

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

    public function getWhatsappLinkAttribute()
    {
        $numero = Configuracion::where('clave', 'whatsapp_numero')->value('valor');
        $mensaje = "Hola, quiero pedir: {$this->nombre} - \${$this->precio}";

        return "https://wa.me/{$numero}?text=" . urlencode($mensaje);
    }

    public function registrarMovimiento(
        string $tipo,
        int $cantidad,
        ?string $motivo,
        ?int $usuarioId
    ): void {
        MovimientoInventario::create([
            'producto_id' => $this->id,
            'usuario_id' => $usuarioId,
            'tipo' => $tipo,
            'cantidad' => $cantidad,
            'motivo' => $motivo,
        ]);

        $this->stock = match ($tipo) {
            'entrada' => $this->stock + $cantidad,
            'salida' => $this->stock - $cantidad,
            'ajuste' => $cantidad,
        };

        $this->save();
    }
}