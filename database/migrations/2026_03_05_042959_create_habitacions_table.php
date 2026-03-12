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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id('id_habitacion'); // PK
            $table->integer('numero')->unique();
            $table->string('tipo', 50);
            $table->decimal('precio', 10, 2);
            $table->integer('capacidad');
            $table->enum('estado', ['disponible', 'ocupada', 'limpieza'])->default('disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
