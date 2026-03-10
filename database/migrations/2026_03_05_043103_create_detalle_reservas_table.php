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
        Schema::create('detalle_reservas', function (Blueprint $table) {
            $table->id('id_detalle'); // PK
            $table->unsignedBigInteger('id_reserva'); // FK
            $table->unsignedBigInteger('id_habitacion'); // FK
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')->onDelete('cascade');
            $table->foreign('id_habitacion')->references('id_habitacion')->on('habitaciones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_reservas');
    }
};
