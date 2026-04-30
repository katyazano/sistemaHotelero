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

    // Legacy alias kept for policy compatibility
    public function esPersonal(): bool
    {
        return false;
    }
}
