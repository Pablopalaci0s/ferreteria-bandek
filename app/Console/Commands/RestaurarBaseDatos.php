<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ConfigurationUrlParser;
use Symfony\Component\Process\Process;

class RestaurarBaseDatos extends Command
{
    protected $signature = 'db:restore {archivo? : Nombre del archivo .sql.gz dentro de la carpeta de backups} {--force : No pedir confirmación}';

    protected $description = 'Restaura la base de datos (MySQL o PostgreSQL) desde un backup .sql.gz. SOBRESCRIBE los datos actuales.';

    public function handle(): int
    {
        $conexion = config('database.default');

        // Ver el comentario equivalente en BackupBaseDatos::handle(): sin
        // esto, con una conexion armada via DB_URL (Neon, Supabase...) los
        // valores de host/puerto quedan en sus defaults (127.0.0.1) en vez
        // de los reales.
        $db = (new ConfigurationUrlParser)->parseConfiguration(
            config("database.connections.{$conexion}")
        );
        $driver = $db['driver'] ?? null;

        if (! in_array($driver, ['mysql', 'pgsql'], true)) {
            $this->error("La restauración solo está preparada para MySQL o PostgreSQL (conexión actual: {$conexion}).");

            return self::FAILURE;
        }

        $carpeta = rtrim(config('backup.path'), '/');
        $archivo = $this->argument('archivo');

        // Sin argumento: mostramos los backups disponibles.
        if (! $archivo) {
            return $this->listarBackups($carpeta, $db['database']);
        }

        $ruta = $carpeta.'/'.basename($archivo);

        if (! is_file($ruta)) {
            $this->error("No existe el backup: {$ruta}");

            return self::FAILURE;
        }

        $this->warn("Vas a restaurar «{$archivo}» sobre la base «{$db['database']}».");
        $this->warn('Esto REEMPLAZA los datos actuales por los del backup.');

        if (! $this->option('force') && ! $this->confirm('¿Continuar?')) {
            $this->line('Cancelado.');

            return self::SUCCESS;
        }

        $credenciales = $driver === 'mysql'
            ? $this->crearArchivoCredencialesMysql($db)
            : $this->crearArchivoCredencialesPgsql($db);

        try {
            $process = $driver === 'mysql'
                ? $this->procesoRestoreMysql($db, $credenciales)
                : $this->procesoRestorePgsql($db, $credenciales);
            $process->setTimeout(600);

            // Descomprimimos el .gz y lo enviamos por la entrada estándar de mysql.
            $gz = gzopen($ruta, 'rb');
            $process->setInput($this->leerGzip($gz));
            $process->run();
            gzclose($gz);

            if (! $process->isSuccessful()) {
                $this->error('Falló la restauración: '.trim($process->getErrorOutput()));

                return self::FAILURE;
            }
        } finally {
            @unlink($credenciales);
        }

        $this->info("Base «{$db['database']}» restaurada desde {$archivo}.");

        return self::SUCCESS;
    }

    private function listarBackups(string $carpeta, string $database): int
    {
        $archivos = glob($carpeta.'/'.$database.'-*.sql.gz') ?: [];
        rsort($archivos);

        if (empty($archivos)) {
            $this->warn("No hay backups en {$carpeta}.");

            return self::SUCCESS;
        }

        $this->info('Backups disponibles (más reciente primero):');

        $filas = array_map(fn ($f) => [
            basename($f),
            round(filesize($f) / 1024, 1).' KB',
            date('d/m/Y H:i', filemtime($f)),
        ], $archivos);

        $this->table(['Archivo', 'Tamaño', 'Fecha'], $filas);
        $this->line('Para restaurar:  php artisan db:restore '.basename($archivos[0]));

        return self::SUCCESS;
    }

    private function leerGzip($gz): \Generator
    {
        while (! gzeof($gz)) {
            yield gzread($gz, 65536);
        }
    }

    private function procesoRestoreMysql(array $db, string $cnf): Process
    {
        return new Process([
            $this->binario('mysql'),
            '--defaults-extra-file='.$cnf,
            $db['database'],
        ]);
    }

    private function procesoRestorePgsql(array $db, string $pgpass): Process
    {
        return new Process(
            command: [
                $this->binario('psql'),
                '--host='.$db['host'],
                '--port='.$db['port'],
                '--username='.$db['username'],
                '--no-password',
                '--quiet',
                $db['database'],
            ],
            env: [
                'PGSSLMODE' => $db['sslmode'] ?? 'require',
                'PGPASSFILE' => $pgpass,
            ],
        );
    }

    private function crearArchivoCredencialesMysql(array $db): string
    {
        $cnf = tempnam(sys_get_temp_dir(), 'rst');
        chmod($cnf, 0600);

        file_put_contents(
            $cnf,
            "[client]\n"
            ."user=\"{$db['username']}\"\n"
            ."password=\"{$db['password']}\"\n"
            ."host=\"{$db['host']}\"\n"
            ."port=\"{$db['port']}\"\n"
        );

        return $cnf;
    }

    private function crearArchivoCredencialesPgsql(array $db): string
    {
        $pgpass = tempnam(sys_get_temp_dir(), 'rst');
        chmod($pgpass, 0600);

        file_put_contents(
            $pgpass,
            "{$db['host']}:{$db['port']}:{$db['database']}:{$db['username']}:{$db['password']}\n"
        );

        return $pgpass;
    }

    private function binario(string $nombre): string
    {
        $dir = trim((string) config('backup.binaries_path'));

        return $dir === '' ? $nombre : rtrim($dir, '/').'/'.$nombre;
    }
}
