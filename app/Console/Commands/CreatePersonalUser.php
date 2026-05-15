<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatePersonalUser extends Command
{
    protected $signature = 'user:create-personal {name} {email} {password}';
    protected $description = 'Crear un usuario de personal (recepcionista)';

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
            'rol'      => 'personal',
        ]);

        $this->info("✓ Usuario personal '{$name}' creado con éxito.");
        return 0;
    }
}
