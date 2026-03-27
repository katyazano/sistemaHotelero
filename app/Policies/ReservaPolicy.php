<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\User;

class ReservaPolicy
{
    /**
     * Determina si el usuario puede ver el listado de reservas.
     */
    public function viewAny(User $user): bool
    {
        // Admin y Personal ven todas. El Huésped verá solo las suyas (lógica en el controlador).
        return true;
    }

    /**
     * Determina si el usuario puede crear una reserva.
     */
    public function create(User $user): bool
    {
        // Todos los usuarios registrados pueden crear reservas.
        return true;
    }

    /**
     * Determina si el usuario puede actualizar la reserva (Check-in/out).
     */
    public function update(User $user, Reserva $reserva): bool
    {
        // Solo el Administrador y el Personal pueden editar estados de reserva.
        return $user->esAdmin() || $user->esPersonal();
    }

    /**
     * Determina si el usuario puede eliminar la reserva.
     */
    public function delete(User $user, Reserva $reserva): bool
    {
        // Acción exclusiva del Administrador para control de auditoría.
        return $user->esAdmin();
    }
}
