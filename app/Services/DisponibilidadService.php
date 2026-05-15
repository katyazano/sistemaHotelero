<?php

namespace App\Services;

use App\Models\DetalleReserva;
use App\Models\Habitacion;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Centraliza la lógica de detección de empalmes de fechas y la búsqueda
 * de habitaciones disponibles en un rango. Es la única fuente de verdad
 * para "esta habitación está libre del día X al día Y".
 *
 * Convención de fechas: la fecha_salida es exclusiva (check-out el día Y
 * deja la habitación libre para una nueva entrada el mismo día Y).
 */
class DisponibilidadService
{
    public const ESTADOS_OCUPANTES = ['confirmada', 'check_in'];

    /**
     * Devuelve true si la habitación está OCUPADA en el rango dado.
     * Si se pasa $excluyendoReservaId, ignora esa reserva (útil al editar).
     *
     * Si $forUpdate=true, el SELECT se hace con lock pesimista para que
     * dos transacciones simultáneas no puedan crear reservas conflictivas.
     */
    public function tieneConflicto(
        int $idHabitacion,
        string $fechaEntrada,
        string $fechaSalida,
        ?int $excluyendoReservaId = null,
        bool $forUpdate = false
    ): bool {
        $query = DetalleReserva::query()
            ->where('id_habitacion', $idHabitacion)
            ->whereHas('reserva', function (Builder $q) use ($fechaEntrada, $fechaSalida, $excluyendoReservaId) {
                $q->whereIn('estado_reserva', self::ESTADOS_OCUPANTES)
                    ->where('fecha_entrada', '<', $fechaSalida)
                    ->where('fecha_salida', '>', $fechaEntrada);

                if ($excluyendoReservaId !== null) {
                    $q->where('id_reserva', '!=', $excluyendoReservaId);
                }
            });

        if ($forUpdate) {
            $query->lockForUpdate();
        }

        return $query->exists();
    }

    /**
     * Devuelve la colección de habitaciones libres en el rango.
     * Usa una sola consulta NOT EXISTS para escalar bien con muchas habitaciones.
     */
    public function habitacionesDisponibles(string $fechaEntrada, string $fechaSalida): Collection
    {
        return Habitacion::query()
            ->where('estado', '!=', 'mantenimiento')
            ->whereNotExists(function ($q) use ($fechaEntrada, $fechaSalida) {
                $q->select(DB::raw(1))
                    ->from('detalle_reservas')
                    ->join('reservas', 'reservas.id_reserva', '=', 'detalle_reservas.id_reserva')
                    ->whereColumn('detalle_reservas.id_habitacion', 'habitaciones.id_habitacion')
                    ->whereIn('reservas.estado_reserva', self::ESTADOS_OCUPANTES)
                    ->where('reservas.fecha_entrada', '<', $fechaSalida)
                    ->where('reservas.fecha_salida', '>', $fechaEntrada);
            })
            ->orderBy('precio')
            ->get();
    }

    /**
     * Calcula noches enteras entre dos fechas (mínimo 1).
     */
    public function calcularNoches(string $fechaEntrada, string $fechaSalida): int
    {
        $noches = Carbon::parse($fechaEntrada)->diffInDays(Carbon::parse($fechaSalida));
        return max(1, (int) $noches);
    }
}
