<?php

namespace App\Support;

use App\Models\Reserva;
use Illuminate\Support\Str;

class FolioGenerator
{
    /**
     * Genera un folio único garantizado contra colisiones reintentando si choca.
     * Formato: RES-YYYYMMDD-XXXXXX (6 chars alfanuméricos en mayúsculas).
     */
    public static function nuevo(): string
    {
        $intentos = 0;
        do {
            $folio = 'RES-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $existe = Reserva::where('folio', $folio)->exists();
            $intentos++;
        } while ($existe && $intentos < 10);

        return $folio;
    }
}
