<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Habitacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function pdf()
    {
        $totalReservas   = Reserva::count();
        $ingresosTotales = Reserva::where('estado_pago', 'pagado')->sum('total');

        // Reservas por habitación
        $porHabitacion = DB::table('detalle_reservas')
            ->join('habitaciones', 'detalle_reservas.id_habitacion', '=', 'habitaciones.id_habitacion')
            ->select('habitaciones.numero', 'habitaciones.tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('habitaciones.id_habitacion', 'habitaciones.numero', 'habitaciones.tipo')
            ->orderByDesc('total')
            ->get();

        // Reservas por mes
        $porMes = Reserva::select(
                DB::raw("DATE_FORMAT(fecha_entrada, '%Y-%m') as mes"),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(total) as ingresos')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $pdf = Pdf::loadView('admin.reporte-pdf', compact(
            'totalReservas', 'ingresosTotales', 'porHabitacion', 'porMes'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('reporte-hotelero-' . now()->format('Y-m-d') . '.pdf');
    }
}
