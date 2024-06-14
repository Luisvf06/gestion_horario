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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->integer('horario_cod');
            $table->char('dia',1);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('asignatura_id');
            $table->unsignedBigInteger('aula_id')->nullable();
            $table->unsignedBigInteger('franja_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('periodo_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
