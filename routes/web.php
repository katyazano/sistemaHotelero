<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController; // Importamos el controlador

// La ruta principal ahora llama al método 'index' del HotelController
Route::get('/', [HotelController::class, 'index']);

// La ruta de contacto se queda igual, regresando la vista directamente
Route::get('/contacto', function () {
    return view('contact');
});