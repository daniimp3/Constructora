<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temporary_passwords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('password'); // Contraseña temporal en texto plano
            $table->timestamp('viewed_at')->nullable(); // Cuándo fue vista por última vez
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('temporary_passwords');
    }
};