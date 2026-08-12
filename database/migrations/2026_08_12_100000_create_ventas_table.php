<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ventas')) {
            return;
        }

        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            // Vendedor que registró la venta.
            $table->foreignId('vendedor_id')
                ->constrained('usuarios')
                ->cascadeOnDelete();

            $table->string('estado', 20)->default('pendiente');
            // valores: pendiente | confirmada | cancelada

            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_telefono', 40)->nullable();

            $table->decimal('total', 12, 2)->default(0);

            $table->text('notas')->nullable();

            // Auditoría de la transición de estado.
            $table->foreignId('confirmada_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();
            $table->timestamp('confirmada_en')->nullable();

            $table->foreignId('cancelada_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();
            $table->timestamp('cancelada_en')->nullable();

            $table->timestamps();

            $table->index(['vendedor_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
