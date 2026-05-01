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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->after('email');
            $table->string('pais', 100)->nullable()->after('telefono');
            $table->string('capital', 100)->nullable()->after('pais');
            $table->string('moneda', 100)->nullable()->after('capital');
            $table->string('idiomas', 255)->nullable()->after('moneda');
            $table->string('zona_horaria', 50)->nullable()->after('idiomas');
            $table->string('bandera_url', 255)->nullable()->after('zona_horaria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn([
                'telefono',
                'pais',
                'capital',
                'moneda',
                'idiomas',
                'zona_horaria',
                'bandera_url'
            ]);
        });
    }
};
