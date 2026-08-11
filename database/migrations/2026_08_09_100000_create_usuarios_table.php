<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla base de usuarios del panel (admin/vendedor). No existía como
     * migración en el repo -- esto la recrea para que una base de datos
     * nueva (producción) quede igual que la de desarrollo. Si la tabla
     * ya existe (como en el entorno de desarrollo actual) no hace nada.
     */
    public function up(): void
    {
        if (Schema::hasTable('usuarios')) {
            return;
        }

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('rol', 20)->default('vendedor');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
