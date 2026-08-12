<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pipeline de imágenes para producción.
 *
 * - Convierte todo a WebP (mucho más liviano; conserva transparencia).
 * - Redimensiona para entrar en un ancho Y alto máximos (nunca agranda).
 * - Corrige la orientación EXIF de fotos de celular.
 * - Nombres de archivo aleatorios y seguros (no expone el nombre original).
 * - Opcionalmente genera un thumbnail en {carpeta}/thumbs/.
 * - Si GD (o WebP) no está disponible, cae con elegancia a JPEG/PNG o al
 *   archivo original, para no romper nunca el formulario.
 */
class ImagenOptimizada
{
    /**
     * Procesa y guarda una imagen subida en el disco "public".
     *
     * @param  string  $carpeta  Ej. "productos", "banners", "categorias"
     * @param  int  $anchoMaximo  Ancho máximo en px
     * @param  int  $altoMaximo  Alto máximo en px
     * @param  int  $calidad  Calidad WebP/JPEG (0-100)
     * @param  int|null  $thumbnail  Si se indica, genera además un thumbnail
     *                               cuadrado-máximo de ese lado en {carpeta}/thumbs/
     * @return string Ruta relativa de la imagen principal dentro del disco "public"
     */
    public static function guardar(
        UploadedFile $archivo,
        string $carpeta,
        int $anchoMaximo = 1600,
        int $altoMaximo = 1600,
        int $calidad = 82,
        ?int $thumbnail = null
    ): string {

        if (! extension_loaded('gd')) {
            return $archivo->store($carpeta, 'public');
        }

        try {
            return self::procesar($archivo, $carpeta, $anchoMaximo, $altoMaximo, $calidad, $thumbnail);
        } catch (\Throwable $e) {
            report($e);

            return $archivo->store($carpeta, 'public');
        }
    }

    /**
     * Devuelve la ruta del thumbnail correspondiente a una imagen principal,
     * o la propia ruta si el thumbnail no existe.
     */
    public static function thumb(?string $ruta): ?string
    {
        if (! $ruta) {
            return $ruta;
        }

        $rutaThumb = self::rutaThumb($ruta);

        return Storage::disk('public')->exists($rutaThumb) ? $rutaThumb : $ruta;
    }

    /**
     * Borra del disco "public" una imagen y su thumbnail (si existen).
     */
    public static function eliminar(?string $ruta): void
    {
        if (! $ruta) {
            return;
        }

        Storage::disk('public')->delete([$ruta, self::rutaThumb($ruta)]);
    }

    private static function rutaThumb(string $ruta): string
    {
        $carpeta = trim(dirname($ruta), '.');
        $archivo = basename($ruta);
        $prefijo = $carpeta === '' ? '' : $carpeta.'/';

        return $prefijo.'thumbs/'.$archivo;
    }

    private static function procesar(
        UploadedFile $archivo,
        string $carpeta,
        int $anchoMaximo,
        int $altoMaximo,
        int $calidad,
        ?int $thumbnail
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
            default => null,
        };

        if (! $origen) {
            return $archivo->store($carpeta, 'public');
        }

        // Corrige rotación si el celular guardó la orientación en el EXIF.
        if ($tipo === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $origen = self::corregirOrientacion($origen, $rutaTemporal);
            $anchoOriginal = imagesx($origen);
            $altoOriginal = imagesy($origen);
        }

        $carpeta = trim($carpeta, '/');
        $extension = self::extensionSalida();
        $nombreArchivo = Str::random(40).'.'.$extension;
        $rutaRelativa = $carpeta.'/'.$nombreArchivo;

        Storage::disk('public')->makeDirectory($carpeta);

        self::renderizar(
            $origen,
            $anchoOriginal,
            $altoOriginal,
            $anchoMaximo,
            $altoMaximo,
            $calidad,
            Storage::disk('public')->path($rutaRelativa),
            $extension
        );

        // Thumbnail opcional.
        if ($thumbnail !== null) {
            Storage::disk('public')->makeDirectory($carpeta.'/thumbs');

            self::renderizar(
                $origen,
                $anchoOriginal,
                $altoOriginal,
                $thumbnail,
                $thumbnail,
                $calidad,
                Storage::disk('public')->path(self::rutaThumb($rutaRelativa)),
                $extension
            );
        }

        imagedestroy($origen);

        return $rutaRelativa;
    }

    /**
     * Redimensiona (solo achica) para entrar en anchoMax x altoMax
     * conservando la proporción, y escribe el archivo en disco.
     */
    private static function renderizar(
        $origen,
        int $anchoOriginal,
        int $altoOriginal,
        int $anchoMaximo,
        int $altoMaximo,
        int $calidad,
        string $rutaCompleta,
        string $extension
    ): void {

        $escala = min(
            1,
            $anchoMaximo / $anchoOriginal,
            $altoMaximo / $altoOriginal
        );

        $anchoNuevo = max(1, (int) round($anchoOriginal * $escala));
        $altoNuevo = max(1, (int) round($altoOriginal * $escala));

        $destino = imagecreatetruecolor($anchoNuevo, $altoNuevo);

        // Conservar transparencia (importante para logos/PNG y WebP con alfa).
        imagealphablending($destino, false);
        imagesavealpha($destino, true);
        $transparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
        imagefilledrectangle($destino, 0, 0, $anchoNuevo, $altoNuevo, $transparente);

        imagecopyresampled(
            $destino, $origen,
            0, 0, 0, 0,
            $anchoNuevo, $altoNuevo,
            $anchoOriginal, $altoOriginal
        );

        if ($extension === 'webp') {
            imagewebp($destino, $rutaCompleta, $calidad);
        } else {
            // Sin WebP: aplanamos sobre blanco y guardamos JPEG.
            $plano = imagecreatetruecolor($anchoNuevo, $altoNuevo);
            $blanco = imagecolorallocate($plano, 255, 255, 255);
            imagefilledrectangle($plano, 0, 0, $anchoNuevo, $altoNuevo, $blanco);
            imagecopy($plano, $destino, 0, 0, 0, 0, $anchoNuevo, $altoNuevo);
            imagejpeg($plano, $rutaCompleta, $calidad);
            imagedestroy($plano);
        }

        imagedestroy($destino);
    }

    private static function extensionSalida(): string
    {
        return function_exists('imagewebp') ? 'webp' : 'jpg';
    }

    private static function corregirOrientacion($imagen, string $ruta)
    {
        try {
            $exif = @exif_read_data($ruta);

            if (empty($exif['Orientation'])) {
                return $imagen;
            }

            $rotada = match ($exif['Orientation']) {
                3 => imagerotate($imagen, 180, 0),
                6 => imagerotate($imagen, -90, 0),
                8 => imagerotate($imagen, 90, 0),
                default => $imagen,
            };

            return $rotada ?: $imagen;
        } catch (\Throwable $e) {
            return $imagen;
        }
    }
}
