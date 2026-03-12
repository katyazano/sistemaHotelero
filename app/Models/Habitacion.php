<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    protected $table = 'habitaciones';
    protected $primaryKey = 'id_habitacion';
    protected $fillable = ['numero', 'tipo', 'precio', 'capacidad', 'estado', 'imagen_url'];

    public function detalles() {
        return $this->hasMany(DetalleReserva::class, 'id_habitacion', 'id_habitacion');
    }
}
