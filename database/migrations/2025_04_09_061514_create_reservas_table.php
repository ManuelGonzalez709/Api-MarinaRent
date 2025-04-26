<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("usuario_id");
            $table->unsignedBigInteger("publicacion_id");
            $table->datetime('fecha_reserva');
            $table->integer('total_pagar')->nullable();
            $table->integer('personas')->nullable();
            $table->timestamps();
            
            // CORREGIDO aquí
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('publicacion_id')->references('id')->on('publicaciones')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
