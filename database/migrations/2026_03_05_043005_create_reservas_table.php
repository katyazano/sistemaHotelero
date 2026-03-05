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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); // PK
            $table->string('folio', 50)->unique();
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->enum('estado_pago', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            $table->enum('estado_reserva', ['confirmada', 'cancelada'])->default('confirmada');
            $table->decimal('total', 10, 2);
            $table->unsignedBigInteger('id_usuario'); // FK
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->timestamps();
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
