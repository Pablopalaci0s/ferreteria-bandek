<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('categorias')) {
            return;
        }

        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('slug');
            $table->string('imagen')->nullable();
            $table->foreignId('categoria_padre_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
