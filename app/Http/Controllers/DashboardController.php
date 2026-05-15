<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Habitacion;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** Panel del administrador */
    public function adminIndex()
    {
        return view('admin.dashboard', [
            'totalReservas'    => Reserva::count(),
            'totalHabitaciones'=> Habitacion::count(),
            'totalUsuarios'    => User::where('rol', 'guest')->count(),
            'ingresosTotales'  => Reserva::where('estado_pago', 'pagado')->sum('total'),
            'reservasRecientes'=> Reserva::with('usuario')->latest()->take(5)->get(),
        ]);
    }

    /** Panel del personal de recepción */
    public function personalIndex()
    {
        $hoy = \Carbon\Carbon::today();

        $llegadasList = Reserva::with('usuario', 'detalles.habitacion')
            ->where('estado_reserva', 'confirmada')
            ->whereDate('fecha_entrada', $hoy)
            ->get();

        $alojadosList = Reserva::with('usuario', 'detalles.habitacion')
            ->where('estado_reserva', 'check_in')
            ->get();

        $salidasVencidas = Reserva::with('usuario', 'detalles.habitacion')
            ->where('estado_reserva', 'check_in')
            ->whereDate('fecha_salida', '<=', $hoy)
            ->get();

        return view('personal.dashboard', [
            'llegadasHoy'          => $llegadasList->count(),
            'salidasHoy'           => $salidasVencidas->count(),
            'alojadosActuales'     => $alojadosList->count(),
            'habitacionesLimpieza' => Habitacion::where('estado', 'limpieza')->count(),
            'habitacionesOcupadas' => Habitacion::where('estado', 'ocupada')->count(),
            'llegadasList'         => $llegadasList,
            'alojadosList'         => $alojadosList,
            'salidasVencidas'      => $salidasVencidas,
            'busqueda'             => '',
        ]);
    }

    /** Panel del huésped */
    public function guestIndex()
    {
        $user = Auth::user();
        $misReservas = Reserva::whereHas('usuario', function ($q) use ($user) {
            $q->where('email', $user->email);
        })->with('detalles.habitacion')->latest()->take(3)->get();

        return view('guest.dashboard', compact('misReservas'));
    }
}
