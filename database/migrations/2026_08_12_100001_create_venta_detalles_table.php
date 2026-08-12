<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('venta_detalles')) {
            return;
        }

        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->cascadeOnDelete();

            // Se conserva la referencia, pero si borran el producto la venta
            // sigue siendo válida gracias al snapshot de nombre/sku/precio.
            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            $table->string('producto_nombre');
            $table->string('producto_sku', 50)->nullable();

            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
    }
};
