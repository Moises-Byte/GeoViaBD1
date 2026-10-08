<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspeccion', function (Blueprint $table) {
            $table->increments('id_inspeccion');
            $table->unsignedInteger('reporte_id_reporte');
            $table->unsignedInteger('usuario_id_usuario');
            $table->date('fecha_inspeccion')->default(DB::raw('SYSDATE'));
            $table->string('resultado', 20);
            $table->string('observaciones', 500)->nullable();

            $table->foreign('reporte_id_reporte', 'inspeccion_reporte_fk')
                ->references('id_reporte')->on('reporte');

            $table->foreign('usuario_id_usuario', 'inspeccion_usuario_fk')
                ->references('id_usuario')->on('usuario');
        });

        DB::statement("
            ALTER TABLE inspeccion
            ADD CONSTRAINT inspeccion_resultado_ck
            CHECK (resultado IN ('APROBADO', 'RECHAZADO'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('inspeccion');
    }
};
