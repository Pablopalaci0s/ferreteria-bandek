<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // "public" es el disco donde se guardan las imágenes de productos,
        // categorías y banners (ver App\Support\ImagenOptimizada). Por
        // defecto es el disco local, pero en un hosting sin disco
        // persistente (ej. Render free tier) hay que apuntarlo a un bucket
        // S3-compatible (Cloudflare R2, Supabase Storage, etc.) con
        // FILESYSTEM_DISK_PUBLIC=s3 + las variables AWS_* de abajo. Ver
        // DEPLOY-CLOUD.md.
        //
        // OJO: las claves de cada driver van en bloques separados a
        // propósito. "root" es solo del driver "local" — el adaptador S3
        // de Flysystem también lee "root" (la usa como prefijo de ruta
        // dentro del bucket), así que si quedara presente con el driver
        // "s3" cada archivo se guardaría con la ruta absoluta del
        // contenedor pegada adelante (ej. "app/storage/app/public/...")
        // en vez de solo "categorias/archivo.webp".
        'public' => array_merge([
            'driver' => env('FILESYSTEM_DISK_PUBLIC', 'local'),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ], env('FILESYSTEM_DISK_PUBLIC', 'local') === 's3' ? [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'url' => env('AWS_URL'),
        ] : [
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
        ]),

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // Google Drive (para las copias off-site de los backups).
        // El driver 'google' se registra en AppServiceProvider.
        'google' => [
            'driver' => 'google',
            'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
