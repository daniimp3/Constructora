<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('reportado_por')->constrained('usuarios');
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('severidad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', ['reportado', 'en_revision', 'resuelto', 'cerrado'])->default('reportado');
            $table->foreignId('asignado_a')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('resuelto_en')->nullable();
            $table->text('notas_resolucion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('incidencias');
    }
};
