<?php

namespace Database\Seeders;

use App\Models\Habitacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear admin de demostración
        User::firstOrCreate(
            ['email' => 'admin@hotel.test'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'rol'      => 'admin',
            ]
        );

        // Crear personal de demostración
        User::firstOrCreate(
            ['email' => 'personal@hotel.test'],
            [
                'name'     => 'Recepcionista',
                'password' => Hash::make('password'),
                'rol'      => 'personal',
            ]
        );

        // Crear huésped de demostración
        User::firstOrCreate(
            ['email' => 'guest@hotel.test'],
            [
                'name'     => 'Cliente Demo',
                'password' => Hash::make('password'),
                'rol'      => 'guest',
            ]
        );

        // Crear algunas habitaciones de ejemplo
        $tipos = ['Simple', 'Doble', 'Suite', 'Deluxe'];
        $precios = [50, 75, 150, 200];

        for ($i = 1; $i <= 12; $i++) {
            $idx = ($i - 1) % count($tipos);
            Habitacion::firstOrCreate(
                ['numero' => $i],
                [
                    'tipo'      => $tipos[$idx],
                    'precio'    => $precios[$idx],
                    'capacidad' => $idx + 1,
                    'estado'    => 'disponible',
                ]
            );
        }
    }
}
