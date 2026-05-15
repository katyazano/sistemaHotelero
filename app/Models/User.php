<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'pais',
        'telefono',
        'fecha_nacimiento',
        'direccion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esGuest(): bool
    {
        return $this->rol === 'guest';
    }

    public function esPersonal(): bool
    {
        return $this->rol === 'personal';
    }

    public function esStaff(): bool
    {
        return in_array($this->rol, ['admin', 'personal'], true);
    }
}
