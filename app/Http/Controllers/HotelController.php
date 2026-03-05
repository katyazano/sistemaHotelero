<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Habitacion; // Importamos el modelo

class HotelController extends Controller
{
    public function index()
    {
        // Traemos todas las habitaciones de la base de datos
        $habitaciones = Habitacion::all();
        
        // Retornamos la vista 'index' y le pasamos la variable $habitaciones
        return view('index', compact('habitaciones'));
    }
}