<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Register
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Password reset placeholder (evita error de ruta 'password.request')
Route::get('/forgot-password', function () {
    return redirect()->route('login');
})->name('password.request');
