<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte', function (Blueprint $table) {
            $table->increments('id_reporte');
            $table->unsignedInteger('usuario_id_usuario');
            $table->unsignedInteger('via_id_via');
            $table->unsignedInteger('tipo_dano_id_tipo_dano');
            $table->string('descripcion', 500);
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->date('fecha_reporte')->default(DB::raw('SYSDATE'));
            $table->string('estado', 30)->default('PENDIENTE');
            $table->integer('prioridad')->default(0);

            $table->foreign('usuario_id_usuario', 'reporte_usuario_fk')
                ->references('id_usuario')->on('usuario');

            $table->foreign('via_id_via', 'reporte_via_fk')
                ->references('id_via')->on('via');

            $table->foreign('tipo_dano_id_tipo_dano', 'reporte_tipo_dano_fk')
                ->references('id_tipo_dano')->on('tipo_dano');
        });

        DB::statement("
            ALTER TABLE reporte ADD CONSTRAINT reporte_latitud_ck
            CHECK (latitud BETWEEN -90 AND 90)
        ");

        DB::statement("
            ALTER TABLE reporte ADD CONSTRAINT reporte_longitud_ck
            CHECK (longitud BETWEEN -180 AND 180)
        ");

        DB::statement("
            ALTER TABLE reporte ADD CONSTRAINT reporte_estado_ck
            CHECK (estado IN (
                'PENDIENTE', 'VALIDADO', 'RECHAZADO',
                'EN REPARACION', 'FINALIZADO'
            ))
        ");

        DB::statement("
            ALTER TABLE reporte ADD CONSTRAINT reporte_prioridad_ck
            CHECK (prioridad >= 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte');
    }
};
