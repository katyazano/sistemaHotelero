<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Reserva - Sistema Hotelero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Sistema Hotelero</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reservas.index') }}">Reservas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="display-4 fw-bold text-center">Detalle de Reserva: {{ $reserva->folio }}</h1>
        <div class="card">
            <div class="card-body">
                <p><strong>Cliente:</strong> {{ $reserva->usuario->nombre }} ({{ $reserva->usuario->email }})</p>
                <p><strong>Teléfono:</strong> {{ $reserva->usuario->telefono ?? 'No especificado' }}</p>
                <p><strong>Fechas:</strong> {{ $reserva->fecha_entrada }} al {{ $reserva->fecha_salida }}</p>
                <p><strong>Noches:</strong> {{ (strtotime($reserva->fecha_salida) - strtotime($reserva->fecha_entrada)) / 86400 }}</p>
                <p><strong>Estado Pago:</strong> <span class="badge bg-{{ $reserva->estado_pago == 'pagado' ? 'success' : ($reserva->estado_pago == 'cancelado' ? 'danger' : 'warning') }}">{{ ucfirst($reserva->estado_pago) }}</span></p>
                <p><strong>Estado Reserva:</strong> <span class="badge bg-{{ $reserva->estado_reserva == 'confirmada' ? 'success' : 'danger' }}">{{ ucfirst($reserva->estado_reserva) }}</span></p>
                <p><strong>Total:</strong> ${{ number_format($reserva->total, 2) }}</p>

                <h4>Habitaciones reservadas</h4>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Habitación</th>
                            <th>Tipo</th>
                            <th>Personas</th>
                            <th>Precio/noche</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reserva->detalles as $detalle)
                        <tr>
                            <td>#{{ $detalle->habitacion->numero }}</td>
                            <td>{{ $detalle->habitacion->tipo }}</td>
                            <td>{{ $detalle->cantidad_personas }}</td>
                            <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td>${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>