<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;

// La ruta principal ahora llama al método 'index' del HotelController
Route::get('/', [HotelController::class, 'index']);

// La ruta de contacto se queda igual, regresando la vista directamente
Route::get('/contacto', function () {
    return view('contact');
});

// Rutas de registro
Route::get('/registro', [UsuarioController::class, 'create'])->name('usuarios.create');
Route::post('/registro', [UsuarioController::class, 'store'])->name('usuarios.store');

// Rutas para reservas
Route::resource('reservas', ReservaController::class);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
