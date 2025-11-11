<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('multimedia_avances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_avance_obra')->constrained('avances_obra')->onDelete('cascade');
            $table->enum('tipo', ['foto', 'nota', 'documento']);
            $table->string('ruta_archivo');
            $table->string('nombre_archivo');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('multimedia_avances');
    }
};
