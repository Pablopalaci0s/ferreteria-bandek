<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registrarDiscoGoogleDrive();
    }

    /**
     * Registra el driver "google" (Google Drive) para el sistema de archivos,
     * usado por las copias off-site de los backups. Solo se registra si el
     * paquete está instalado, así el resto de la app funciona sin él.
     */
    private function registrarDiscoGoogleDrive(): void
    {
        if (! class_exists(GoogleDriveAdapter::class)) {
            return;
        }

        Storage::extend('google', function ($app, array $config) {
            $client = new Client;
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new Drive($client);

            $adapter = new GoogleDriveAdapter(
                $service,
                $config['folderId'] ?? '/',
            );

            return new FilesystemAdapter(new Filesystem($adapter), $adapter, $config);
        });
    }
}
