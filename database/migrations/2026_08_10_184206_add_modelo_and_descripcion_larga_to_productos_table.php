<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'modelo')) {
                $table->string('modelo')->nullable()->after('sku');
            }

            if (! Schema::hasColumn('productos', 'descripcion_larga')) {
                $table->text('descripcion_larga')->nullable()->after('descripcion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['modelo', 'descripcion_larga']);
        });
    }
};