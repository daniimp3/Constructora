<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alertas_presupuesto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('id_presupuesto')->nullable()->constrained('presupuestos')->onDelete('set null');
            $table->decimal('porcentaje_umbral', 5, 2);
            $table->decimal('porcentaje_actual', 5, 2);
            $table->enum('tipo_alerta', ['advertencia', 'excedido']);
            $table->boolean('fue_leida')->default(false);
            $table->text('mensaje');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas_presupuesto');
    }
};
