<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Ejecutar los seeders manuales (los 3 usuarios fijos y las 3 habitaciones)
        $this->call([
            UsuarioSeeder::class,
            HabitacionSeeder::class,
        ]);

        // 2. Ejecutar el Factory para crear 5 administradores aleatorios
        Usuario::factory(5)->create([
            'rol' => 'Administrador'
        ]);

        // 3. Ejecutar el Factory para crear 50 clientes (huéspedes) aleatorios
        Usuario::factory(50)->create([
            'rol' => 'Huesped'
        ]);
    }
}