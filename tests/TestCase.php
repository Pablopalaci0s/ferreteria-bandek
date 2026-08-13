<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Blindaje: los tests SIEMPRE corren sobre SQLite en memoria, pase lo que
     * pase con la configuración cacheada (bootstrap/cache/config.php).
     *
     * Sin esto, un config cacheado que apunte a MySQL haría que RefreshDatabase
     * ejecute migrate:fresh sobre la base REAL y borre todos los datos.
     * Se fuerza acá, antes de que corran los traits (RefreshDatabase).
     */
    protected function refreshApplication(): void
    {
        parent::refreshApplication();

        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $conexion = $this->app['config']->get('database.default');
        $base = $this->app['config']->get("database.connections.{$conexion}.database");

        if ($conexion !== 'sqlite' || $base !== ':memory:') {
            throw new RuntimeException(
                'ABORTADO: los tests deben correr sobre sqlite en memoria, '
                ."pero apuntan a [{$conexion}:{$base}]. Se evita destruir datos reales."
            );
        }
    }
}
