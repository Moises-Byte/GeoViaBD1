<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuadrilla', function (Blueprint $table) {
            $table->increments('id_cuadrilla');
            $table->string('nombre', 100);
            $table->string('responsable', 100);
            $table->string('telefono', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuadrilla');
    }
};
