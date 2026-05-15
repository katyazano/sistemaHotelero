<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin {name} {email} {password}';
    protected $description = 'Crear un usuario administrador';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        if (User::where('email', $email)->exists()) {
            $this->error("El email {$email} ya existe.");
            return 1;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'rol'      => 'admin',
        ]);

        $this->info("✓ Usuario admin '{$name}' creado con éxito.");
        return 0;
    }
}
