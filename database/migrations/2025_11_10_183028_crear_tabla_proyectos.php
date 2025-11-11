<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada');
            $table->date('fecha_fin_real')->nullable();
            $table->enum('estado', ['activo', 'terminado', 'pausado', 'cancelado'])->default('activo');
            $table->decimal('presupuesto_total', 15, 2);
            $table->foreignId('id_administrador')->constrained('usuarios')->onDelete('cascade');
            $table->decimal('porcentaje_avance', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proyectos');
    }
};
