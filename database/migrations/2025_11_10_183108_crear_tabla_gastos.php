<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->foreignId('id_presupuesto')->nullable()->constrained('presupuestos')->onDelete('set null');
            $table->string('concepto');
            $table->decimal('monto', 12, 2);
            $table->date('fecha_gasto');
            $table->string('categoria');
            $table->text('descripcion')->nullable();
            $table->string('ruta_comprobante')->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gastos');
    }
};