<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory; 

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $fillable = [
        'nombre', 
        'email', 
        'password', 
        'rol',
        'telefono',
        'pais',
        'capital',
        'moneda',
        'idiomas',
        'zona_horaria',
        'bandera_url'
    ];

    public function reservas() {
        return $this->hasMany(Reserva::class, 'id_usuario', 'id_usuario');
    }
    
    public function reportes() {
        return $this->hasMany(Reporte::class, 'id_usuario_admin', 'id_usuario');
    }
}