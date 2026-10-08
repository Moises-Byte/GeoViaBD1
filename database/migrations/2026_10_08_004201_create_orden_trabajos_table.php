<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orden_trabajo', function (Blueprint $table) {
            $table->increments('id_orden');
            $table->unsignedInteger('reporte_id_reporte');
            $table->unsignedInteger('cuadrilla_id_cuadrilla');
            $table->unsignedInteger('usuario_id_usuario');
            $table->date('fecha_asignacion')->default(DB::raw('SYSDATE'));
            $table->date('fecha_finalizacion')->nullable();
            $table->string('estado', 30)->default('PENDIENTE');
            $table->integer('avance')->default(0);
            $table->string('observaciones', 500)->nullable();

            $table->foreign('reporte_id_reporte', 'orden_reporte_fk')
                ->references('id_reporte')->on('reporte');

            $table->foreign('cuadrilla_id_cuadrilla', 'orden_cuadrilla_fk')
                ->references('id_cuadrilla')->on('cuadrilla');

            $table->foreign('usuario_id_usuario', 'orden_supervisor_fk')
                ->references('id_usuario')->on('usuario');
        });

        DB::statement("
            ALTER TABLE orden_trabajo ADD CONSTRAINT orden_estado_ck
            CHECK (estado IN ('PENDIENTE', 'EN PROCESO', 'FINALIZADA'))
        ");

        DB::statement("
            ALTER TABLE orden_trabajo ADD CONSTRAINT orden_avance_ck
            CHECK (avance BETWEEN 0 AND 100)
        ");

        DB::statement("
            ALTER TABLE orden_trabajo ADD CONSTRAINT orden_estado_fecha_ck
            CHECK (
                (estado = 'FINALIZADA' AND fecha_finalizacion IS NOT NULL)
                OR
                (estado <> 'FINALIZADA' AND fecha_finalizacion IS NULL)
            )
        ");

        DB::statement("
            ALTER TABLE orden_trabajo ADD CONSTRAINT orden_fechas_ck
            CHECK (
                fecha_finalizacion IS NULL
                OR fecha_finalizacion >= fecha_asignacion
            )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_trabajo');
    }
};
