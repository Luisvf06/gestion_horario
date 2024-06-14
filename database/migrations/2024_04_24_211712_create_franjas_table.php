<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('franjas', function (Blueprint $table) {
            $table->id();
            $table->integer('franja_cod'); // Agregado
            $table->string('descripcion');
            $table->time('hora_desde');
            $table->time('hora_hasta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franjas');
    }
};
