<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * En la base de desarrollo, "tipo" quedó como un ENUM heredado
     * ('entrada','salida','ajuste') creado fuera de las migraciones del repo.
     * El módulo de Ventas necesita además 'venta', y el inventario usa
     * 'devolucion' y 'perdida'. Se convierte a VARCHAR para no volver a
     * chocar con una lista cerrada cada vez que se agregue un tipo nuevo.
     */
    public function up(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->string('tipo', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->string('tipo', 20)->change();
        });
    }
};
