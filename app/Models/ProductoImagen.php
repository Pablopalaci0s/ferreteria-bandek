<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    public $timestamps = false;

    protected $fillable = ['producto_id', 'ruta', 'orden'];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}