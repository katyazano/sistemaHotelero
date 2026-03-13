<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservaController; 
// Importamos los controlador

// La ruta principal ahora llama al método 'index' del HotelController
Route::get('/', [HotelController::class, 'index']);

// La ruta de contacto se queda igual, regresando la vista directamente
Route::get('/contacto', function () {
    return view('contact');
});

// Rutas para reservas
Route::resource('reservas', ReservaController::class);