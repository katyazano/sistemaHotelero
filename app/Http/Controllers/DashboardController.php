<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Habitacion;
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
            'totalUsuarios'    => Usuario::count(),
            'ingresosTotales'  => Reserva::where('estado_pago', 'pagado')->sum('total'),
            'reservasRecientes'=> Reserva::with('usuario')->latest()->take(5)->get(),
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
