<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografia', function (Blueprint $table) {
            $table->increments('id_foto');
            $table->unsignedInteger('reporte_id_reporte');
            $table->string('ruta_foto', 255);

            $table->foreign('reporte_id_reporte', 'fotografia_reporte_fk')
                ->references('id_reporte')
                ->on('reporte')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografia');
    }
};
