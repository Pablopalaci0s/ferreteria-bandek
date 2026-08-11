<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagenOptimizada
{
    /**
     * Redimensiona y comprime una imagen subida, y la guarda en el disco "public".
     * Si algo falla (o GD no está disponible), guarda el archivo original tal cual,
     * para nunca romper el formulario por esto.
     *
     * @param  UploadedFile  $archivo
     * @param  string  $carpeta  Ej. "productos", "banners", "categorias"
     * @param  int  $anchoMaximo  Ancho máximo en px (nunca agranda una imagen más chica)
     * @param  int  $calidad  Calidad JPEG/WebP (0-100)
     * @return string  Ruta relativa dentro del disco "public" (igual que ->store())
     */
    public static function guardar(
        UploadedFile $archivo,
        string $carpeta,
        int $anchoMaximo = 1600,
        int $calidad = 82
    ): string {

        if (! extension_loaded('gd')) {
            return $archivo->store($carpeta, 'public');
        }

        try {
            return self::procesar($archivo, $carpeta, $anchoMaximo, $calidad);
        } catch (\Throwable $e) {
            report($e);

            return $archivo->store($carpeta, 'public');
        }
    }

    private static function procesar(
        UploadedFile $archivo,
        string $carpeta,
        int $anchoMaximo,
        int $calidad
    ): string {

        $rutaTemporal = $archivo->getRealPath();
        $info = @getimagesize($rutaTemporal);

        if (! $info) {
            return $archivo->store($carpeta, 'public');
        }

        [$anchoOriginal, $altoOriginal] = $info;
        $tipo = $info[2] ?? null;

        $origen = match ($tipo) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($rutaTemporal),
            IMAGETYPE_PNG => @imagecreatefrompng($rutaTemporal),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($rutaTemporal) : null,
            IMAGETYPE_GIF => @imagecreatefromgif($rutaTemporal),
            default => null,
        };

        if (! $origen) {
            return $archivo->store($carpeta, 'public');
        }

        // Corrige la rotación si el celular guardó la orientación en el EXIF
        if ($tipo === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $origen = self::corregirOrientacion($origen, $rutaTemporal);
        }

        // Solo achica, nunca agranda
        if ($anchoOriginal > $anchoMaximo) {
            $altoNuevo = (int) round($altoOriginal * ($anchoMaximo / $anchoOriginal));
            $anchoNuevo = $anchoMaximo;
        } else {
            $anchoNuevo = $anchoOriginal;
            $altoNuevo = $altoOriginal;
        }

        $destino = imagecreatetruecolor($anchoNuevo, $altoNuevo);

        // Conservar transparencia en PNG
        if ($tipo === IMAGETYPE_PNG) {
            imagealphablending($destino, false);
            imagesavealpha($destino, true);
            $transparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
            imagefilledrectangle($destino, 0, 0, $anchoNuevo, $altoNuevo, $transparente);
        }

        imagecopyresampled(
            $destino, $origen,
            0, 0, 0, 0,
            $anchoNuevo, $altoNuevo,
            $anchoOriginal, $altoOriginal
        );

        // PNG con transparencia real se guarda como PNG; el resto, como JPEG (más liviano)
        $tienTransparencia = $tipo === IMAGETYPE_PNG && self::tieneTransparencia($origen, $anchoOriginal, $altoOriginal);

        $extension = $tienTransparencia ? 'png' : 'jpg';
        $nombreArchivo = Str::random(40) . '.' . $extension;
        $rutaRelativa = trim($carpeta, '/') . '/' . $nombreArchivo;
        $rutaCompleta = Storage::disk('public')->path($rutaRelativa);

        Storage::disk('public')->makeDirectory($carpeta);

        if ($tienTransparencia) {
            imagepng($destino, $rutaCompleta, 6);
        } else {
            // Si no hay transparencia, aplanamos sobre blanco antes de convertir a JPEG
            if ($tipo === IMAGETYPE_PNG) {
                $plano = imagecreatetruecolor($anchoNuevo, $altoNuevo);
                $blanco = imagecolorallocate($plano, 255, 255, 255);
                imagefilledrectangle($plano, 0, 0, $anchoNuevo, $altoNuevo, $blanco);
                imagecopy($plano, $destino, 0, 0, 0, 0, $anchoNuevo, $altoNuevo);
                imagedestroy($destino);
                $destino = $plano;
            }

            imagejpeg($destino, $rutaCompleta, $calidad);
        }

        imagedestroy($origen);
        imagedestroy($destino);

        return $rutaRelativa;
    }

    private static function tieneTransparencia($imagen, int $ancho, int $alto): bool
    {
        // Revisamos una muestra de píxeles (no todos, para no ser lentos) buscando alpha < 127
        $pasoX = max(1, (int) ($ancho / 40));
        $pasoY = max(1, (int) ($alto / 40));

        for ($x = 0; $x < $ancho; $x += $pasoX) {
            for ($y = 0; $y < $alto; $y += $pasoY) {
                $color = imagecolorat($imagen, $x, $y);
                $alpha = ($color >> 24) & 0x7F;

                if ($alpha > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function corregirOrientacion($imagen, string $ruta)
    {
        try {
            $exif = @exif_read_data($ruta);

            if (empty($exif['Orientation'])) {
                return $imagen;
            }

            return match ($exif['Orientation']) {
                3 => imagerotate($imagen, 180, 0),
                6 => imagerotate($imagen, -90, 0),
                8 => imagerotate($imagen, 90, 0),
                default => $imagen,
            };
        } catch (\Throwable $e) {
            return $imagen;
        }
    }
}
