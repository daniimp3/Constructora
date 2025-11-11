<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reportes_proyecto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->enum('tipo_reporte', ['gastos', 'avance', 'general', 'comparativo']);
            $table->string('ruta_archivo');
            $table->string('nombre_archivo');
            $table->enum('formato', ['pdf', 'excel']);
            $table->foreignId('generado_por')->constrained('usuarios');
            $table->date('fecha_reporte_desde')->nullable();
            $table->date('fecha_reporte_hasta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reportes_proyecto');
    }
};