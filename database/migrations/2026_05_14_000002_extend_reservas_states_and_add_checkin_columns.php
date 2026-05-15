<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliamos estado_reserva con los nuevos estados operativos.
        DB::statement(
            "ALTER TABLE reservas MODIFY estado_reserva ".
            "ENUM('pendiente','confirmada','check_in','check_out','cancelada') NOT NULL DEFAULT 'confirmada'"
        );

        Schema::table('reservas', function (Blueprint $table) {
            $table->timestamp('check_in_at')->nullable()->after('estado_reserva');
            $table->timestamp('check_out_at')->nullable()->after('check_in_at');
            $table->unsignedBigInteger('check_in_by')->nullable()->after('check_out_at');
            $table->unsignedBigInteger('check_out_by')->nullable()->after('check_in_by');

            $table->foreign('check_in_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('check_out_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['check_in_by']);
            $table->dropForeign(['check_out_by']);
            $table->dropColumn(['check_in_at', 'check_out_at', 'check_in_by', 'check_out_by']);
        });

        DB::table('reservas')
            ->whereIn('estado_reserva', ['pendiente', 'check_in', 'check_out'])
            ->update(['estado_reserva' => 'confirmada']);

        DB::statement(
            "ALTER TABLE reservas MODIFY estado_reserva ".
            "ENUM('confirmada','cancelada') NOT NULL DEFAULT 'confirmada'"
        );
    }
};
