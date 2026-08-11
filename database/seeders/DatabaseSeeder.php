<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * No crea usuarios de prueba. El primer usuario admin se crea
     * a mano (Tinker, un insert directo, o copiando el usuario real
     * desde la base de datos de desarrollo al exportarla).
     */
    public function run(): void
    {
        //
    }
}
