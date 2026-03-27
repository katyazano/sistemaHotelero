<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Muestra el tablero principal según el rol del usuario.
     */
    public function index()
    {
        $user = Auth::user();

        // Lógica para el Administrador
        if ($user->rol === 'administrador') {
            return view('admin.dashboard', [
                'usuarios' => User::all(),
                'totalReservas' => Reserva::count()
            ]);
        } 
        
        // Lógica para el Personal (Recepcionista)
        if ($user->rol === 'personal') {
            return view('personal.dashboard', [
                'reservasActivas' => Reserva::where('estado', 'confirmada')->get()
            ]);
        }

        // Lógica para el Huésped
        return view('huesped.dashboard', [
            'misReservas' => Reserva::where('user_id', $user->id)->get()
        ]);
    }
}