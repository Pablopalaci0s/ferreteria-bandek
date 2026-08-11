<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unidades_medida')) {
            return;
        }

        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('abreviatura', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};
