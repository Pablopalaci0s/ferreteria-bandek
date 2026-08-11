<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['nombre', 'slug', 'imagen', 'categoria_padre_id', 'activo', 'orden'];

    public function categoriaPadre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre_id');
    }

    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre_id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}