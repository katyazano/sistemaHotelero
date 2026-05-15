<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Operación de recepción: check-in y check-out digital.
 * Disponible para personal y administradores.
 */
class CheckInOutController extends Controller
{
    /**
     * Tablero diario: reservas que llegan hoy, que están alojadas y que salen hoy.
     */
    public function index(Request $request)
    {
        $hoy = Carbon::today();
        $busqueda = trim((string) $request->input('q', ''));

        $base = Reserva::with('usuario', 'detalles.habitacion')
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $q->where(function ($w) use ($busqueda) {
                    $w->where('folio', 'like', "%{$busqueda}%")
                        ->orWhereHas('usuario', fn ($u) =>
                            $u->where('nombre', 'like', "%{$busqueda}%")
                              ->orWhere('email', 'like', "%{$busqueda}%")
                        );
                });
            });

        $llegadasHoy = (clone $base)
            ->where('estado_reserva', 'confirmada')
            ->whereDate('fecha_entrada', '<=', $hoy)
            ->whereDate('fecha_salida', '>=', $hoy)
            ->orderBy('fecha_entrada')
            ->get();

        $alojados = (clone $base)
            ->where('estado_reserva', 'check_in')
            ->orderBy('fecha_salida')
            ->get();

        $salidasHoy = (clone $base)
            ->where('estado_reserva', 'check_in')
            ->whereDate('fecha_salida', '<=', $hoy)
            ->get();

        return view('checkinout.index', compact('llegadasHoy', 'alojados', 'salidasHoy', 'busqueda'));
    }

    public function checkIn(Reserva $reserva)
    {
        if (!$reserva->puedeHacerCheckIn()) {
            return back()->withErrors(['error' => 'Esta reserva no puede hacer check-in en este momento.']);
        }

        DB::transaction(function () use ($reserva) {
            $reserva->update([
                'estado_reserva' => 'check_in',
                'check_in_at'    => now(),
                'check_in_by'    => Auth::id(),
            ]);

            // Marcamos las habitaciones de la reserva como ocupadas.
            $idsHab = $reserva->detalles()->pluck('id_habitacion');
            Habitacion::whereIn('id_habitacion', $idsHab)->update(['estado' => 'ocupada']);
        });

        return back()->with('success', "Check-in registrado para folio {$reserva->folio}.");
    }

    public function checkOut(Reserva $reserva)
    {
        if (!$reserva->puedeHacerCheckOut()) {
            return back()->withErrors(['error' => 'Esta reserva no puede hacer check-out en este momento.']);
        }

        DB::transaction(function () use ($reserva) {
            $reserva->update([
                'estado_reserva' => 'check_out',
                'check_out_at'   => now(),
                'check_out_by'   => Auth::id(),
            ]);

            // Las habitaciones quedan en limpieza tras check-out.
            $idsHab = $reserva->detalles()->pluck('id_habitacion');
            Habitacion::whereIn('id_habitacion', $idsHab)->update(['estado' => 'limpieza']);
        });

        return back()->with('success', "Check-out registrado para folio {$reserva->folio}.");
    }

    /**
     * Permite al personal marcar una habitación como disponible tras limpieza.
     */
    public function liberarHabitacion(Habitacion $habitacion)
    {
        $habitacion->update(['estado' => 'disponible']);
        return back()->with('success', "Habitación #{$habitacion->numero} marcada como disponible.");
    }
}
