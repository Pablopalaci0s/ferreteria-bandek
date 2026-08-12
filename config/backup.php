<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Carpeta donde se guardan los backups
    |--------------------------------------------------------------------------
    | Por defecto storage/app/backups. Está fuera del control de versiones.
    */
    'path' => storage_path('app/backups'),

    /*
    |--------------------------------------------------------------------------
    | Retención (días)
    |--------------------------------------------------------------------------
    | Los backups más viejos que esta cantidad de días se borran solos en
    | cada ejecución. Con 7 se mantienen las copias de la última semana.
    */
    'retention_days' => (int) env('DB_BACKUP_RETENTION_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Ruta de los binarios de MySQL
    |--------------------------------------------------------------------------
    | Carpeta que contiene "mysqldump" y "mysql". Dejar vacío si ya están en
    | el PATH del sistema. En este equipo (Herd/macOS) suele ser:
    |   /usr/local/mysql/bin
    */
    'binaries_path' => env('DB_DUMP_BINARY_PATH', ''),

];
