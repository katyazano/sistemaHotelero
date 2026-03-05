<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        // 1. Usuario Administrador (Requisito del ejemplo)
        Usuario::create([
            'nombre' => 'Pedro Sánchez',
            'email' => 'pedro.sanchez@miProyecto.com',
            'password' => Hash::make('p3Dr054nchez'),
            'rol' => 'Administrador'
        ]);

        // 2. Usuario de Personal
        Usuario::create([
            'nombre' => 'Paola Lino',
            'email' => 'recepcion@miproyecto.com',
            'password' => Hash::make('secreto123'),
            'rol' => 'Personal'
        ]);

        // 3. Usuario Huésped
        Usuario::create([
            'nombre' => 'Katheryn Azano',
            'email' => 'katheryn@cliente.com',
            'password' => Hash::make('cliente123'),
            'rol' => 'Huesped'
        ]);
    }
}