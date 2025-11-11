<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
            $table->date('fecha_asistencia');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'permiso'])->default('presente');
            $table->text('notas')->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamps();
            
            $table->unique(['id_usuario', 'fecha_asistencia', 'id_proyecto']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};
