<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { color: #1e3a5f; font-size: 20px; }
        h2 { color: #1e3a5f; font-size: 14px; border-bottom: 2px solid #1e3a5f; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #1e3a5f; color: white; padding: 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background: #f5f5f5; }
        .stat-box { display: inline-block; background: #f0f4ff; border: 1px solid #c0d0f0; padding: 12px 20px; margin: 8px; border-radius: 6px; }
        .stat-value { font-size: 22px; font-weight: bold; color: #1e3a5f; }
        .footer { margin-top: 30px; font-size: 10px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte General - Sistema Hotelero</h1>
    <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Resumen General</h2>
    <div>
        <div class="stat-box">
            <div>Total de Reservas</div>
            <div class="stat-value">{{ $totalReservas }}</div>
        </div>
        <div class="stat-box">
            <div>Ingresos Totales (pagados)</div>
            <div class="stat-value">${{ number_format($ingresosTotales, 2) }} MXN</div>
        </div>
    </div>

    <h2 style="margin-top:24px;">Reservas por Habitación</h2>
    <table>
        <thead>
            <tr><th>#</th><th>Tipo</th><th>Total Reservas</th></tr>
        </thead>
        <tbody>
            @forelse($porHabitacion as $row)
            <tr>
                <td>{{ $row->numero }}</td>
                <td>{{ $row->tipo }}</td>
                <td>{{ $row->total }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin datos</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Reservas por Mes</h2>
    <table>
        <thead>
            <tr><th>Mes</th><th>Reservas</th><th>Ingresos</th></tr>
        </thead>
        <tbody>
            @forelse($porMes as $row)
            <tr>
                <td>{{ $row->mes }}</td>
                <td>{{ $row->total }}</td>
                <td>${{ number_format($row->ingresos, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="3">Sin datos</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Sistema Hotelero &copy; {{ date('Y') }} — Reporte confidencial</div>
</body>
</html>
