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
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('descripcion');
            $table->datetime('fecha_evento');
            $table->enum('tipo', ['alquilable', 'informativo']);
            $table->integer('precio')->nullable();
            $table->text('imagen')->nullable();
            $table->integer('aforo_maximo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
