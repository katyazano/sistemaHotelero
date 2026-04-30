<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Usuario;
use App\Models\Habitacion;
use App\Models\Reserva;
use App\Models\DetalleReserva;

class DemoSeeder extends Seeder
{
    /**
     * Seed demo data for testing (May 8 demo).
     */
    public function run(): void
    {
        // 1. Create Users (for authentication)
        $admin = User::create([
            'name' => 'Admin Demo',
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'pais' => 'México',
        ]);

        $guest1 = User::create([
            'name' => 'Guest One',
            'email' => 'guest1@test.com',
            'password' => Hash::make('guest123'),
            'rol' => 'guest',
            'pais' => 'España',
        ]);

        $guest2 = User::create([
            'name' => 'Guest Two',
            'email' => 'guest2@test.com',
            'password' => Hash::make('guest123'),
            'rol' => 'guest',
            'pais' => 'Argentina',
        ]);

        // 2. Create Usuarios (legacy table for reservations)
        $usuario1 = Usuario::create([
            'nombre' => 'Guest One',
            'email' => 'guest1@test.com',
            'password' => Hash::make('guest123'),
            'rol' => 'Huesped',
        ]);

        $usuario2 = Usuario::create([
            'nombre' => 'Guest Two',
            'email' => 'guest2@test.com',
            'password' => Hash::make('guest123'),
            'rol' => 'Huesped',
        ]);

        // 3. Create Habitaciones
        $hab1 = Habitacion::create([
            'numero' => '101',
            'tipo' => 'Sencilla',
            'precio' => 800.00,
            'capacidad' => 2,
            'estado' => 'disponible',
            'imagen_url' => '/storage/imagen_habitaciones/sencilla.png',
        ]);

        $hab2 = Habitacion::create([
            'numero' => '201',
            'tipo' => 'Doble',
            'precio' => 1200.00,
            'capacidad' => 4,
            'estado' => 'disponible',
            'imagen_url' => '/storage/imagen_habitaciones/doble.png',
        ]);

        $hab3 = Habitacion::create([
            'numero' => '301',
            'tipo' => 'Penthouse',
            'precio' => 2500.00,
            'capacidad' => 6,
            'estado' => 'disponible',
            'imagen_url' => '/storage/imagen_habitaciones/penthouse.png',
        ]);

        // 4. Create Reservas
        $reserva1 = Reserva::create([
            'folio' => 'RES-20260501-A1B2',
            'fecha_entrada' => '2026-05-10',
            'fecha_salida' => '2026-05-13',
            'estado_pago' => 'pagado',
            'estado_reserva' => 'confirmada',
            'total' => 2400.00,
            'id_usuario' => $usuario1->id_usuario,
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva1->id_reserva,
            'id_habitacion' => $hab1->id_habitacion,
            'precio_unitario' => 800.00,
            'subtotal' => 2400.00, // 3 nights
        ]);

        $reserva2 = Reserva::create([
            'folio' => 'RES-20260502-C3D4',
            'fecha_entrada' => '2026-05-15',
            'fecha_salida' => '2026-05-17',
            'estado_pago' => 'pendiente',
            'estado_reserva' => 'confirmada',
            'total' => 2400.00,
            'id_usuario' => $usuario1->id_usuario,
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva2->id_reserva,
            'id_habitacion' => $hab2->id_habitacion,
            'precio_unitario' => 1200.00,
            'subtotal' => 2400.00, // 2 nights
        ]);

        $reserva3 = Reserva::create([
            'folio' => 'RES-20260503-E5F6',
            'fecha_entrada' => '2026-05-20',
            'fecha_salida' => '2026-05-22',
            'estado_pago' => 'pendiente',
            'estado_reserva' => 'confirmada',
            'total' => 5000.00,
            'id_usuario' => $usuario2->id_usuario,
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva3->id_reserva,
            'id_habitacion' => $hab3->id_habitacion,
            'precio_unitario' => 2500.00,
            'subtotal' => 5000.00, // 2 nights
        ]);

        $reserva4 = Reserva::create([
            'folio' => 'RES-20260504-G7H8',
            'fecha_entrada' => '2026-05-25',
            'fecha_salida' => '2026-05-28',
            'estado_pago' => 'cancelado',
            'estado_reserva' => 'cancelada',
            'total' => 3600.00,
            'id_usuario' => $usuario2->id_usuario,
        ]);

        DetalleReserva::create([
            'id_reserva' => $reserva4->id_reserva,
            'id_habitacion' => $hab2->id_habitacion,
            'precio_unitario' => 1200.00,
            'subtotal' => 3600.00, // 3 nights
        ]);

        $this->command->info('✓ Demo data seeded successfully!');
    }
}
