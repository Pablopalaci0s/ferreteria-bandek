<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla base de productos. Las columnas agregadas más adelante
     * (modelo, descripcion_larga, precio_oferta) las ponen sus propias
     * migraciones incrementales, que ya están guardadas con
     * Schema::hasColumn() y corren después de esta.
     */
    public function up(): void
    {
        if (Schema::hasTable('productos')) {
            return;
        }

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50);
            $table->string('nombre');
            $table->string('slug');
            $table->string('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->decimal('costo', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(0);

            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignId('unidad_medida_id')->constrained('unidades_medida');

            $table->string('imagen_principal')->nullable();
            $table->decimal('peso', 8, 2)->nullable();

            $table->boolean('activo')->default(true);
            $table->boolean('destacado')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
