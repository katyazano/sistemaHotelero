<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Habitacion;
use App\Models\User;

class HabitacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
   public function viewAny(User $user) {
    // Todos pueden ver habitaciones (Huésped, Personal, Admin)
        return true; 
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Habitacion $habitacion): bool
    {
        // Todos los usuarios autenticados pueden ver el detalle de una habitación
        return true; 
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user) {
    // Solo el administrador puede crear habitaciones
        return $user->esAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Habitacion $habitacion) {
    // El personal solo puede actualizar el estado (limpieza/disponibilidad)
    // El admin puede editar todo
        return $user->esAdmin() || $user->esPersonal();
    }
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Habitacion $habitacion): bool
    {
        // IMPORTANTE: Cambia 'false' por esta validación para que el Admin pueda borrar
        return $user->esAdmin();
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Habitacion $habitacion): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Habitacion $habitacion): bool
    {
        return false;
    }
}
