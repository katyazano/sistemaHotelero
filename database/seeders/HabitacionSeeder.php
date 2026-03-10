<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Habitacion;

class HabitacionSeeder extends Seeder
{
    public function run()
    {
        Habitacion::create([
            'numero' => 101, 
            'tipo' => 'Sencilla', 
            'precio' => 650.00, 
            'capacidad' => 2, 
            'estado' => 'disponible'
        ]);
        
        Habitacion::create([
            'numero' => 205, 
            'tipo' => 'Doble', 
            'precio' => 1100.00, 
            'capacidad' => 4, 
            'estado' => 'ocupada'
        ]);
        
        Habitacion::create([
            'numero' => 501, 
            'tipo' => 'Penthouse', 
            'precio' => 4500.00, 
            'capacidad' => 6, 
            'estado' => 'limpieza'
        ]);
    }
}