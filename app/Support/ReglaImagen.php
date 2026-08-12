<?php

namespace App\Support;

/**
 * Regla de validación única para todas las subidas de imágenes del panel.
 *
 * - Solo JPG, PNG y WebP (bloquea SVG y otros formatos que pueden llevar
 *   scripts embebidos -> evita XSS almacenado).
 * - Valida el contenido real del archivo (mimetypes), no solo la extensión.
 * - Límite de peso razonable (5 MB) antes de comprimir.
 * - Límite de dimensiones: frena imágenes gigantes que reventarían la
 *   memoria de PHP al procesarlas (decompression bomb / DoS).
 */
class ReglaImagen
{
    public const FORMATOS = ['jpeg', 'jpg', 'png', 'webp'];

    public const PESO_MAXIMO_KB = 5120;   // 5 MB

    public const DIMENSION_MAXIMA = 6000; // px

    /**
     * Devuelve el array de reglas para una imagen.
     *
     * @param  bool  $requerida  Si el campo es obligatorio (crear) o no (editar).
     * @return array<int, string>
     */
    public static function reglas(bool $requerida = true): array
    {
        return array_filter([
            $requerida ? 'required' : 'nullable',
            'image',
            'mimes:'.implode(',', self::FORMATOS),
            'mimetypes:image/jpeg,image/png,image/webp',
            'max:'.self::PESO_MAXIMO_KB,
            'dimensions:max_width='.self::DIMENSION_MAXIMA.',max_height='.self::DIMENSION_MAXIMA,
        ]);
    }
}
