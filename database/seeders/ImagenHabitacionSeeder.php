<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Habitacion;

class ImagenHabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagenes = [
            'Sencilla' => '/storage/imagen_habitaciones/sencilla.png',
            'Doble'    => '/storage/imagen_habitaciones/doble.png',
            'Penthouse' => '/storage/imagen_habitaciones/penthouse.png',
        ];

        foreach ($imagenes as $tipo => $ruta) {
            $habitacion = Habitacion::where('tipo', $tipo)->first();

            if ($habitacion) {
                $habitacion->imagen_url = $ruta;
                $habitacion->save();
                $this->command->info("Imagen asignada a habitación {$tipo}");
            } else {
                $this->command->warn("No se encontró habitación de tipo {$tipo}");
            }
        }
    }
}
