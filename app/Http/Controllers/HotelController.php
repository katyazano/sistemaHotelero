<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Services\DisponibilidadService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $habitaciones = Habitacion::orderBy('numero')->get();
        return view('habitaciones.index', compact('habitaciones'));
    }

    /**
     * Catálogo público con motor de búsqueda de disponibilidad por fechas.
     * Si el usuario no envía fechas, mostramos todas las habitaciones (modo catálogo).
     * Si envía fechas válidas, filtramos por habitaciones libres en ese rango.
     */
    public function publicIndex(Request $request, DisponibilidadService $disponibilidad)
    {
        $fechaEntrada = $request->input('fecha_entrada');
        $fechaSalida  = $request->input('fecha_salida');
        $busquedaActiva = false;
        $errorBusqueda = null;

        if ($fechaEntrada || $fechaSalida) {
            $validator = validator(
                $request->only('fecha_entrada', 'fecha_salida'),
                [
                    'fecha_entrada' => 'required|date|after_or_equal:today',
                    'fecha_salida'  => 'required|date|after:fecha_entrada',
                ],
                [],
                [
                    'fecha_entrada' => 'fecha de entrada',
                    'fecha_salida'  => 'fecha de salida',
                ]
            );

            if ($validator->fails()) {
                $errorBusqueda = $validator->errors()->first();
                $habitaciones = collect();
            } else {
                $habitaciones = $disponibilidad->habitacionesDisponibles($fechaEntrada, $fechaSalida);
                $busquedaActiva = true;
            }
        } else {
            $habitaciones = Habitacion::orderBy('precio')->get();
        }

        return view('index', [
            'habitaciones'   => $habitaciones,
            'fechaEntrada'   => $fechaEntrada,
            'fechaSalida'    => $fechaSalida,
            'busquedaActiva' => $busquedaActiva,
            'errorBusqueda'  => $errorBusqueda,
            'minDate'        => Carbon::today()->toDateString(),
        ]);
    }
}
