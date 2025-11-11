<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('avances_obra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('id_tarea')->nullable()->constrained('tareas')->onDelete('set null');
            $table->foreignId('id_supervisor')->constrained('usuarios');
            $table->date('fecha_avance');
            $table->text('descripcion');
            $table->text('notas')->nullable();
            $table->decimal('porcentaje_avance', 5, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('avances_obra');
    }
};
