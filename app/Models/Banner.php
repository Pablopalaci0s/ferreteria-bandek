<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'titulo',
        'zona',
        'imagen',
        'link',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Zonas de publicidad disponibles en el sitio y el tamaño de imagen
     * recomendado para cada una (en píxeles). El ancho/alto se usan para
     * redimensionar la imagen al subirla (ImagenOptimizada nunca agranda,
     * solo achica para entrar en esa caja manteniendo la proporción).
     */
    public const ZONAS = [
        'hero_principal' => [
            'etiqueta' => 'Banner principal (carrusel de arriba)',
            'ancho' => 1200,
            'alto' => 480,
            'ayuda' => 'Imagen de 1200×480px (relación 2.5:1). Es la más grande, arriba de todo en la página principal.',
        ],
        'hero_secundario' => [
            'etiqueta' => 'Banner lateral (junto al principal)',
            'ancho' => 600,
            'alto' => 480,
            'ayuda' => 'Imagen de 600×480px (relación 1.25:1). Se muestra a la par del banner principal.',
        ],
        'promocion' => [
            'etiqueta' => 'Tarjeta de promoción',
            'ancho' => 500,
            'alto' => 400,
            'ayuda' => 'Imagen de 500×400px (relación 1.25:1). Se muestran varias en fila, tipo "más vendido en...".',
        ],
        'franja' => [
            'etiqueta' => 'Banner ancho de sección',
            'ancho' => 1600,
            'alto' => 300,
            'ayuda' => 'Imagen de 1600×300px (relación 5.3:1, panorámica y baja). Ocupa todo el ancho de la página.',
        ],
    ];
}
