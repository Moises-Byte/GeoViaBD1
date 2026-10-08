<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('via', function (Blueprint $table) {
            $table->increments('id_via');
            $table->string('nombre_via', 150);
            $table->string('nivel_trafico', 20);
            $table->date('fecha_ultimo_mantenimiento')->nullable();
        });

        DB::statement("
            ALTER TABLE via
            ADD CONSTRAINT via_trafico_ck
            CHECK (nivel_trafico IN ('ALTO', 'MEDIO', 'BAJO'))
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('via');
    }
};
