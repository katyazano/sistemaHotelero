<?php

/**
 * Script para crear usuario administrador rápidamente
 * Ejecutar con: php crear_admin_rapido.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Verificar si ya existe
    $existente = User::where('email', 'admin@hotel.com')->first();
    
    if ($existente) {
        echo "❌ Ya existe un usuario con el email admin@hotel.com\n";
        echo "   ID: {$existente->id}\n";
        echo "   Nombre: {$existente->name}\n";
        echo "   Rol: {$existente->rol}\n\n";
        
        // Actualizar a admin si no lo es
        if ($existente->rol !== 'admin') {
            $existente->rol = 'admin';
            $existente->save();
            echo "✅ Usuario actualizado a rol 'admin'\n";
        }
        
        echo "\n🔑 Credenciales:\n";
        echo "   Email: admin@hotel.com\n";
        echo "   Password: admin123\n";
        exit(0);
    }
    
    // Crear nuevo usuario admin
    $admin = User::create([
        'name' => 'Administrador',
        'email' => 'admin@hotel.com',
        'password' => Hash::make('admin123'),
        'rol' => 'admin',
        'pais' => 'México',
        'telefono' => '+52 555 123 4567',
        'fecha_nacimiento' => '1985-01-15',
        'direccion' => 'Av. Principal 100, Ciudad de México',
    ]);
    
    echo "✅ Usuario administrador creado exitosamente!\n\n";
    echo "📋 Detalles:\n";
    echo "   ID: {$admin->id}\n";
    echo "   Nombre: {$admin->name}\n";
    echo "   Email: {$admin->email}\n";
    echo "   Rol: {$admin->rol}\n\n";
    echo "🔑 Credenciales de acceso:\n";
    echo "   Email: admin@hotel.com\n";
    echo "   Password: admin123\n\n";
    echo "🌐 Accede en: http://localhost:8000/login\n";
    
} catch (\Exception $e) {
    echo "❌ Error al crear usuario: " . $e->getMessage() . "\n";
    exit(1);
}
