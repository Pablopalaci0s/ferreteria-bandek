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

    /*
    |--------------------------------------------------------------------------
    | Copia externa (off-site)
    |--------------------------------------------------------------------------
    | Además de guardar el backup en disco local, se lo sube a un "disco" de
    | Laravel (ej. 'google' para Google Drive, 's3', etc.). Si queda vacío,
    | el backup solo se guarda localmente.
    |
    |   upload_disk   -> nombre del disco en config/filesystems.php
    |   upload_folder -> subcarpeta dentro de ese disco
    */
    'upload_disk' => env('DB_BACKUP_UPLOAD_DISK', ''),

    'upload_folder' => env('DB_BACKUP_UPLOAD_FOLDER', 'bandek-backups'),

];
