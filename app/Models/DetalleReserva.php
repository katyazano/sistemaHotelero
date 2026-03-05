<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReserva extends Model
{
    use HasFactory;

    protected $table = 'detalle_reservas';
    protected $primaryKey = 'id_detalle';
    
    // Los campos que se pueden llenar masivamente
    protected $fillable = [
        'id_reserva', 
        'id_habitacion', 
        'precio_unitario', 
        'subtotal'
    ];

    // Relación: Un detalle pertenece a una reserva
    public function reserva() {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    // Relación: Un detalle pertenece a una habitación
    public function habitacion() {
        return $this->belongsTo(Habitacion::class, 'id_habitacion', 'id_habitacion');
    }
}