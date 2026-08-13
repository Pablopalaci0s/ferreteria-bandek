<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

class BackupBaseDatos extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Genera un backup comprimido de la base de datos MySQL y rota los antiguos.';

    public function handle(): int
    {
        $conexion = config('database.default');
        $db = config("database.connections.{$conexion}");

        if (($db['driver'] ?? null) !== 'mysql') {
            $this->error("El backup solo está preparado para MySQL (conexión actual: {$conexion}).");

            return self::FAILURE;
        }

        $carpeta = config('backup.path');

        if (! is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombre = $db['database'].'-'.now()->format('Y-m-d_His').'.sql.gz';
        $destino = rtrim($carpeta, '/').'/'.$nombre;

        // Credenciales en un archivo temporal (modo 600) para que NO aparezcan
        // en la lista de procesos ni en los logs.
        $cnf = $this->crearArchivoCredenciales($db);

        $gz = gzopen($destino, 'wb9');
        $errores = '';

        try {
            $process = new Process([
                $this->binario('mysqldump'),
                '--defaults-extra-file='.$cnf,
                '--single-transaction',  // consistente sin bloquear tablas InnoDB
                '--quick',
                '--routines',
                '--triggers',
                '--no-tablespaces',
                '--set-gtid-purged=OFF', // evita el error GTID_PURGED al restaurar
                $db['database'],
            ]);

            $process->setTimeout(600);

            $process->run(function ($tipo, $buffer) use ($gz, &$errores) {
                if ($tipo === Process::OUT) {
                    gzwrite($gz, $buffer);
                } else {
                    $errores .= $buffer;
                }
            });

            gzclose($gz);

            if (! $process->isSuccessful()) {
                @unlink($destino);
                $this->error('Falló mysqldump: '.trim($errores ?: $process->getErrorOutput()));

                return self::FAILURE;
            }
        } finally {
            @unlink($cnf);
        }

        $tamano = $this->formatearTamano(filesize($destino));
        $this->info("Backup creado: {$nombre} ({$tamano})");

        $this->rotarAntiguos($carpeta, $db['database']);

        $this->subirCopiaExterna($destino, $nombre, $db['database']);

        return self::SUCCESS;
    }

    /**
     * Sube el backup a un disco externo (Google Drive, S3, etc.) si está
     * configurado, y aplica la misma rotación por días allá. Nunca hace
     * fallar el comando: el backup local ya está a salvo.
     */
    private function subirCopiaExterna(string $rutaLocal, string $nombre, string $database): void
    {
        $nombreDisco = trim((string) config('backup.upload_disk'));

        if ($nombreDisco === '') {
            return;
        }

        $carpeta = trim(config('backup.upload_folder'), '/');
        $rutaRemota = ($carpeta === '' ? '' : $carpeta.'/').$nombre;

        try {
            $stream = fopen($rutaLocal, 'rb');
            Storage::disk($nombreDisco)->writeStream($rutaRemota, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            $this->info("Copia subida a «{$nombreDisco}»: {$rutaRemota}");

            $this->rotarRemotos($nombreDisco, $carpeta, $database);
        } catch (Throwable $e) {
            // El backup local sí quedó bien; solo avisamos de la copia externa.
            $this->warn('No se pudo subir la copia externa: '.$e->getMessage());
            logger()->warning('Backup off-site falló: '.$e->getMessage());
        }
    }

    private function rotarRemotos(string $nombreDisco, string $carpeta, string $database): void
    {
        $dias = config('backup.retention_days');
        $limite = now()->subDays($dias)->getTimestamp();

        $disco = Storage::disk($nombreDisco);

        foreach ($disco->files($carpeta) as $archivo) {
            if (! str_contains(basename($archivo), $database.'-')) {
                continue;
            }

            if ($disco->lastModified($archivo) < $limite) {
                $disco->delete($archivo);
            }
        }
    }

    private function crearArchivoCredenciales(array $db): string
    {
        $cnf = tempnam(sys_get_temp_dir(), 'bkp');
        chmod($cnf, 0600);

        $contenido = "[client]\n"
            ."user=\"{$db['username']}\"\n"
            ."password=\"{$db['password']}\"\n"
            ."host=\"{$db['host']}\"\n"
            ."port=\"{$db['port']}\"\n";

        file_put_contents($cnf, $contenido);

        return $cnf;
    }

    private function binario(string $nombre): string
    {
        $dir = trim((string) config('backup.binaries_path'));

        return $dir === '' ? $nombre : rtrim($dir, '/').'/'.$nombre;
    }

    /**
     * Borra los backups más viejos que la retención configurada.
     */
    private function rotarAntiguos(string $carpeta, string $database): void
    {
        $dias = config('backup.retention_days');
        $limite = now()->subDays($dias)->getTimestamp();

        $borrados = 0;

        foreach (glob(rtrim($carpeta, '/').'/'.$database.'-*.sql.gz') as $archivo) {
            if (filemtime($archivo) < $limite) {
                @unlink($archivo);
                $borrados++;
            }
        }

        if ($borrados > 0) {
            $this->line("Rotación: {$borrados} backup(s) con más de {$dias} días eliminados.");
        }
    }

    private function formatearTamano(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
