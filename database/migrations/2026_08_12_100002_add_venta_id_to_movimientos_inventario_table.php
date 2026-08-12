<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('movimientos_inventario', 'venta_id')) {
            return;
        }

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->foreignId('venta_id')
                ->nullable()
                ->after('usuario_id')
                ->constrained('ventas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('movimientos_inventario', 'venta_id')) {
            return;
        }

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropConstrainedForeignId('venta_id');
        });
    }
};
