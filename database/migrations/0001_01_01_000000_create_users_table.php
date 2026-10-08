<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->string('nombre', 100);
            $table->string('correo', 100)
                ->unique('usuario_correo_uq');
            $table->string('telefono', 20)->nullable();
            $table->string('contrasena', 255);
            $table->string('rol', 20)->default('VECINO');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
        });

        DB::statement("
            ALTER TABLE usuario
            ADD CONSTRAINT usuario_rol_ck
            CHECK (
                rol IN (
                    'AUTORIDAD',
                    'INSPECTOR',
                    'SUPERVISOR',
                    'VECINO'
                )
            )
        ");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 100)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuario');
    }
};
