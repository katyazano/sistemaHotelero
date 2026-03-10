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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago'); // PK
            $table->unsignedBigInteger('id_reserva'); // FK
            $table->decimal('monto', 10, 2);
            $table->dateTime('fecha_pago');
            $table->string('metodo_pago', 50);
            $table->foreign('id_reserva')->references('id_reserva')->on('reservas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
