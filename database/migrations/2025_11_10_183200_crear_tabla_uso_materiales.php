<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('uso_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('id_material')->constrained('materiales')->onDelete('cascade');
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->decimal('cantidad', 10, 2);
            $table->date('fecha_uso');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('uso_materiales');
    }
};
