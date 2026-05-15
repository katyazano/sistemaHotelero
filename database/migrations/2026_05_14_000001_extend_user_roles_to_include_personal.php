<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliamos el ENUM 'rol' en la tabla users para incluir 'personal'.
        // Usamos SQL directo porque Doctrine/Laravel no soporta modificación de ENUM.
        DB::statement("ALTER TABLE users MODIFY rol ENUM('admin','personal','guest') NOT NULL DEFAULT 'guest'");
    }

    public function down(): void
    {
        // Antes de revertir, reasignamos cualquier 'personal' a 'admin' para no romper el ENUM original.
        DB::table('users')->where('rol', 'personal')->update(['rol' => 'admin']);
        DB::statement("ALTER TABLE users MODIFY rol ENUM('admin','guest') NOT NULL DEFAULT 'guest'");
    }
};
