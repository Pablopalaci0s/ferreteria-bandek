<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

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

        return self::SUCCESS;
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
