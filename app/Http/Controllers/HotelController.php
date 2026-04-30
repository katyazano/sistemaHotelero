<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion; // Importamos el modelo

class HotelController extends Controller
{
    public function index()
    {
        // Admin view - Traemos todas las habitaciones de la base de datos
        $habitaciones = Habitacion::all();
        
        // Retornamos la vista 'habitaciones.index' para admin
        return view('habitaciones.index', compact('habitaciones'));
    }

    public function publicIndex()
    {
        // Public view - Traemos todas las habitaciones disponibles
        $habitaciones = Habitacion::all();
        
        // Retornamos la vista 'index' pública
        return view('index', compact('habitaciones'));
    }
}