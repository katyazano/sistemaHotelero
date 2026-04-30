<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HabitacionController;
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
    ->name('habitaciones.index');

Route::get('/habitaciones/crear', [HabitacionController::class, 'create'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones.create');

Route::post('/habitaciones', [HabitacionController::class, 'store'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones.store');

Route::get('/habitaciones/{habitacion}/editar', [HabitacionController::class, 'edit'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones.edit');

Route::put('/habitaciones/{habitacion}', [HabitacionController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones.update');

Route::delete('/habitaciones/{habitacion}', [HabitacionController::class, 'destroy'])
    ->middleware(['auth', 'role:admin'])
    ->name('habitaciones.destroy');

// Reservas CRUD (admin)
Route::resource('reservas', ReservaController::class)
    ->middleware(['auth', 'role:admin']);

// Reportes PDF (admin)
Route::get('/admin/reportes/pdf', [ReporteController::class, 'pdf'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.reportes.pdf');

require __DIR__.'/auth.php';
