<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e3a5f; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .body { background: #f9f9f9; padding: 24px; border: 1px solid #ddd; }
        .footer { background: #eee; padding: 12px; text-align: center; font-size: 12px; border-radius: 0 0 8px 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #1e3a5f; color: white; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>¡Reserva Confirmada!</h2>
        <p>Folio: {{ $reserva->folio }}</p>
    </div>
    <div class="body">
        <p>Estimado/a <strong>{{ $reserva->usuario->nombre }}</strong>,</p>
        <p>Nos complace confirmar su reserva. A continuación encontrará los detalles:</p>

        <table>
            <tr><th>Folio</th><td>{{ $reserva->folio }}</td></tr>
            <tr><th>Fecha de Entrada</th><td>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</td></tr>
            <tr><th>Fecha de Salida</th><td>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</td></tr>
            <tr><th>Total</th><td>${{ number_format($reserva->total, 2) }} MXN</td></tr>
        </table>

        @if($reserva->detalles->count())
        <h4 style="margin-top:16px;">Habitaciones reservadas:</h4>
        <table>
            <tr><th>Habitación</th><th>Tipo</th><th>Personas</th><th>Subtotal</th></tr>
            @foreach($reserva->detalles as $detalle)
            <tr>
                <td>#{{ $detalle->habitacion->numero ?? 'N/A' }}</td>
                <td>{{ $detalle->habitacion->tipo ?? 'N/A' }}</td>
                <td>{{ $detalle->cantidad_personas }}</td>
                <td>${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </table>
        @endif

        <p style="margin-top:20px;">Si tiene alguna pregunta, no dude en contactarnos.</p>
        <p>¡Esperamos su visita!</p>
    </div>
    <div class="footer">Sistema Hotelero &copy; {{ date('Y') }}</div>
</div>
</body>
</html>
