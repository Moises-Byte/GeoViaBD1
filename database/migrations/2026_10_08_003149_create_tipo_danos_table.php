<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_dano', function (Blueprint $table) {
            $table->increments('id_tipo_dano');
            $table->string('nombre', 50)
                ->unique('tipo_dano_nombre_uq');
            $table->string('descripcion', 255)->nullable();
            $table->char('activo', 1)->default('S');
        });

        DB::statement("
            ALTER TABLE tipo_dano
            ADD CONSTRAINT tipo_dano_activo_ck
            CHECK (activo IN ('S', 'N'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_dano');
    }
};
