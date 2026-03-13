<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    protected $fillable = ['folio', 'fecha_entrada', 'fecha_salida', 'estado_pago', 'estado_reserva', 'total', 'id_usuario', 'imagen_url'];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
    public function detalles() {
        return $this->hasMany(DetalleReserva::class, 'id_reserva', 'id_reserva');
    }
    public function pagos() {
        return $this->hasMany(Pago::class, 'id_reserva', 'id_reserva');
    }
}
