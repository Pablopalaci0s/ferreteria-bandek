<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('solicitudes_precio')) {
            return;
        }

        Schema::create('solicitudes_precio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('producto_id')
                ->constrained('productos')
                ->cascadeOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->decimal('precio_actual', 10, 2);
            $table->decimal('precio_nuevo', 10, 2);
            $table->decimal('precio_oferta_actual', 10, 2)->nullable();
            $table->decimal('precio_oferta_nuevo', 10, 2)->nullable();

            $table->string('estado', 20)->default('pendiente');
            // valores esperados: pendiente | aprobado | rechazado

            $table->foreignId('revisado_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->timestamp('revisado_en')->nullable();

            $table->timestamps();

            $table->index(['producto_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_precio');
    }
};
