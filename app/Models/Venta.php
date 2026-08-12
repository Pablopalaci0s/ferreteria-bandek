<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';

    public const ESTADO_CONFIRMADA = 'confirmada';

    public const ESTADO_CANCELADA = 'cancelada';

    protected $fillable = [
        'vendedor_id',
        'estado',
        'cliente_nombre',
        'cliente_telefono',
        'total',
        'notas',
        'confirmada_por',
        'confirmada_en',
        'cancelada_por',
        'cancelada_en',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'confirmada_en' => 'datetime',
        'cancelada_en' => 'datetime',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function confirmadaPor()
    {
        return $this->belongsTo(User::class, 'confirmada_por');
    }

    public function canceladaPor()
    {
        return $this->belongsTo(User::class, 'cancelada_por');
    }

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', self::ESTADO_PENDIENTE);
    }

    public function scopeConfirmadas($query)
    {
        return $query->where('estado', self::ESTADO_CONFIRMADA);
    }

    public function estaPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    /**
     * Recalcula el total a partir de los detalles ya cargados/guardados.
     */
    public function recalcularTotal(): void
    {
        $this->total = $this->detalles()->sum('subtotal');
        $this->save();
    }
}
