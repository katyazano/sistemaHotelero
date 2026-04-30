<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;

// Ruta raíz → login
Route::get('/', function () {
    return redirect()->route('login');
});

// Habitaciones públicas
Route::get('/habitaciones-publicas', [HotelController::class, 'publicIndex'])->name('habitaciones.public');

// Contacto (pública)
Route::get('/contacto', function () {
    return view('contact');
});

// ── Rutas protegidas (auth) ──────────────────────────────────────────────────

// Dashboard admin
Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');

// Dashboard guest
Route::get('/dashboard', [DashboardController::class, 'guestIndex'])
    ->middleware(['auth', 'role:guest'])
    ->name('guest.dashboard');

// Mis Reservas (guest)
Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])
    ->middleware(['auth', 'role:guest'])
    ->name('mis-reservas');

// Crear reserva (guest)
Route::get('/reservas/crear/{habitacion}', [ReservaController::class, 'guestCreate'])
    ->middleware(['auth', 'role:guest'])
    ->name('reservas.guest.create');

Route::post('/reservas/guardar', [ReservaController::class, 'guestStore'])
    ->middleware(['auth', 'role:guest'])
    ->name('reservas.guest.store');

// Cancelar reserva propia (guest)
Route::patch('/mis-reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])
    ->middleware(['auth', 'role:guest'])
    ->name('reservas.cancelar');

// Habitaciones (admin)
Route::get('/habitaciones', [HotelController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones');

// Reservas CRUD (admin)
Route::resource('reservas', ReservaController::class)
    ->middleware(['auth', 'role:admin']);

// Reportes PDF (admin)
Route::get('/admin/reportes/pdf', [ReporteController::class, 'pdf'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.reportes.pdf');

require __DIR__.'/auth.php';
