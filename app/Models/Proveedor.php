<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'contacto_nombre',
        'telefono',
        'email',
        'direccion',
        'activo'
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}