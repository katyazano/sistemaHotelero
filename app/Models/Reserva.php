<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';
    protected $fillable = [
        'folio',
        'fecha_entrada',
        'fecha_salida',
        'estado_pago',
        'estado_reserva',
        'total',
        'id_usuario',
        'imagen_url',
        'check_in_at',
        'check_out_at',
        'check_in_by',
        'check_out_by',
    ];

    protected $casts = [
        'fecha_entrada' => 'date',
        'fecha_salida'  => 'date',
        'check_in_at'   => 'datetime',
        'check_out_at'  => 'datetime',
        'total'         => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReserva::class, 'id_reserva', 'id_reserva');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_reserva', 'id_reserva');
    }

    public function checkInBy()
    {
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function checkOutBy()
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function puedeHacerCheckIn(): bool
    {
        return $this->estado_reserva === 'confirmada' && is_null($this->check_in_at);
    }

    public function puedeHacerCheckOut(): bool
    {
        return $this->estado_reserva === 'check_in' && is_null($this->check_out_at);
    }
}
