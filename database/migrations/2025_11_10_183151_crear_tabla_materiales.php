<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('unidad'); // kg, m3, piezas, litros, etc.
            $table->decimal('costo_unitario', 10, 2)->default(0);
            $table->decimal('existencia', 10, 2)->default(0);
            $table->decimal('existencia_minima', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('materiales');
    }
};
