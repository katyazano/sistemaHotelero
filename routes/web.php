<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HabitacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\CheckInOutController;

// Ruta raíz → catálogo público
Route::get('/', fn () => redirect()->route('habitaciones.public'));

// Habitaciones públicas con buscador de disponibilidad
Route::get('/habitaciones-publicas', [HotelController::class, 'publicIndex'])->name('habitaciones.public');

// Contacto (pública)
Route::get('/contacto', fn () => view('contact'))->name('contacto');

// ── Rutas protegidas ─────────────────────────────────────────────────────────

// Dashboards
Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])
    ->middleware(['auth', 'role:admin'])->name('admin.dashboard');

Route::get('/personal/dashboard', [DashboardController::class, 'personalIndex'])
    ->middleware(['auth', 'role:personal'])->name('personal.dashboard');

Route::get('/dashboard', [DashboardController::class, 'guestIndex'])
    ->middleware(['auth', 'role:guest'])->name('guest.dashboard');

// Mis Reservas (guest)
Route::get('/mis-reservas', [ReservaController::class, 'misReservas'])
    ->middleware(['auth', 'role:guest'])->name('mis-reservas');

Route::get('/reservas/crear/{habitacion}', [ReservaController::class, 'guestCreate'])
    ->middleware(['auth', 'role:guest'])->name('reservas.guest.create');

Route::post('/reservas/guardar', [ReservaController::class, 'guestStore'])
    ->middleware(['auth', 'role:guest'])->name('reservas.guest.store');

Route::patch('/mis-reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])
    ->middleware(['auth', 'role:guest'])->name('reservas.cancelar');

Route::get('/mis-reservas/{reserva}/pagar', [ReservaController::class, 'showPago'])
    ->middleware(['auth', 'role:guest'])->name('reservas.pagar.form');

Route::post('/mis-reservas/{reserva}/pagar', [ReservaController::class, 'pagarSimulado'])
    ->middleware(['auth', 'role:guest'])->name('reservas.pagar');

// Habitaciones (admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/habitaciones', [HotelController::class, 'index'])->name('habitaciones.index');
    Route::get('/habitaciones/crear', [HabitacionController::class, 'create'])->name('habitaciones.create');
    Route::post('/habitaciones', [HabitacionController::class, 'store'])->name('habitaciones.store');
    Route::get('/habitaciones/{habitacion}/editar', [HabitacionController::class, 'edit'])->name('habitaciones.edit');
    Route::put('/habitaciones/{habitacion}', [HabitacionController::class, 'update'])->name('habitaciones.update');
    Route::delete('/habitaciones/{habitacion}', [HabitacionController::class, 'destroy'])->name('habitaciones.destroy');
});

// Reservas CRUD (admin)
Route::resource('reservas', ReservaController::class)
    ->middleware(['auth', 'role:admin']);

// Check-in / Check-out (personal y admin)
Route::middleware(['auth', 'role:admin,personal'])->group(function () {
    Route::get('/operacion/checkinout', [CheckInOutController::class, 'index'])->name('checkinout.index');
    Route::post('/operacion/checkinout/{reserva}/checkin', [CheckInOutController::class, 'checkIn'])->name('checkinout.checkin');
    Route::post('/operacion/checkinout/{reserva}/checkout', [CheckInOutController::class, 'checkOut'])->name('checkinout.checkout');
    Route::post('/operacion/habitaciones/{habitacion}/liberar', [CheckInOutController::class, 'liberarHabitacion'])->name('checkinout.liberar');
});

// Reportes PDF (admin)
Route::get('/admin/reportes/pdf', [ReporteController::class, 'pdf'])
    ->middleware(['auth', 'role:admin'])->name('admin.reportes.pdf');

require __DIR__.'/auth.php';
