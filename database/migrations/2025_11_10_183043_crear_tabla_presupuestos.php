<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_proyecto')->constrained('proyectos')->onDelete('cascade');
            $table->string('categoria'); // materiales, mano_obra, equipos, otros
            $table->string('concepto');
            $table->decimal('monto_estimado', 12, 2);
            $table->decimal('monto_actual', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('presupuestos');
    }
};
