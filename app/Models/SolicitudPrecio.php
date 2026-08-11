<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudPrecio extends Model
{
    protected $table = 'solicitudes_precio';

    protected $fillable = [
        'producto_id',
        'usuario_id',
        'precio_actual',
        'precio_nuevo',
        'precio_oferta_actual',
        'precio_oferta_nuevo',
        'estado',
        'revisado_por',
        'revisado_en',
    ];

    protected $casts = [
        'precio_actual' => 'decimal:2',
        'precio_nuevo' => 'decimal:2',
        'precio_oferta_actual' => 'decimal:2',
        'precio_oferta_nuevo' => 'decimal:2',
        'revisado_en' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
