<?php

/**
 * Script para actualizar usuario existente con campos nuevos
 * Ejecutar con: php actualizar_usuario_existente.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

try {
    // Buscar usuario admin@test.com
    $user = User::where('email', 'admin@test.com')->first();
    
    if (!$user) {
        echo "❌ Usuario admin@test.com no encontrado\n";
        echo "\n💡 Usuarios disponibles:\n";
        $users = User::all();
        foreach ($users as $u) {
            echo "   - {$u->email} (Rol: {$u->rol})\n";
        }
        exit(1);
    }
    
    echo "✅ Usuario encontrado: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Rol actual: {$user->rol}\n\n";
    
    // Actualizar campos si están vacíos
    $actualizado = false;
    
    if (empty($user->telefono)) {
        $user->telefono = '+52 555 000 0000';
        $actualizado = true;
    }
    
    if (empty($user->fecha_nacimiento)) {
        $user->fecha_nacimiento = '1985-01-01';
        $actualizado = true;
    }
    
    if (empty($user->direccion)) {
        $user->direccion = 'Dirección de prueba';
        $actualizado = true;
    }
    
    if (empty($user->pais)) {
        $user->pais = 'México';
        $actualizado = true;
    }
    
    // Asegurar que sea admin
    if ($user->rol !== 'admin') {
        $user->rol = 'admin';
        $actualizado = true;
    }
    
    if ($actualizado) {
        $user->save();
        echo "✅ Usuario actualizado con campos nuevos\n\n";
    } else {
        echo "ℹ️  Usuario ya tiene todos los campos\n\n";
    }
    
    echo "📋 Información completa:\n";
    echo "   Nombre: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Rol: {$user->rol}\n";
    echo "   País: {$user->pais}\n";
    echo "   Teléfono: {$user->telefono}\n";
    echo "   Fecha Nacimiento: {$user->fecha_nacimiento}\n";
    echo "   Dirección: {$user->direccion}\n\n";
    
    echo "🔑 Puedes acceder con:\n";
    echo "   Email: {$user->email}\n";
    echo "   Password: (tu contraseña actual)\n\n";
    echo "🌐 Accede en: http://localhost:8000/login\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
