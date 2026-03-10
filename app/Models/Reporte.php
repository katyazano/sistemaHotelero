<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';
    protected $primaryKey = 'id_reporte';

    protected $fillable = [
        'tipo_reporte', 
        'fecha_generacion', 
        'datos', 
        'id_usuario_admin'
    ];

    // Relación: Un reporte pertenece a un usuario (administrador)
    public function administrador() {
        // Apuntamos 'id_usuario_admin' (FK local) hacia 'id_usuario' (PK en usuarios)
        return $this->belongsTo(Usuario::class, 'id_usuario_admin', 'id_usuario');
    }
}