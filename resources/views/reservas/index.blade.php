<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas - Sistema Hotelero</title>
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
        <h1 class="display-4 fw-bold text-center">Listado de Reservas</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="text-end mb-3">
            <a href="{{ route('reservas.create') }}" class="btn btn-primary">Nueva Reserva</a>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Total</th>
                    <th>Estado Pago</th>
                    <th>Estado Reserva</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $reserva)
                <tr>
                    <td>{{ $reserva->folio }}</td>
                    <td>
                        {{ $reserva->usuario->nombre ?? 'N/A' }}<br>
                        <small>{{ $reserva->usuario->email ?? '' }}</small>
                    </td>
                    <td>{{ $reserva->fecha_entrada }}</td>
                    <td>{{ $reserva->fecha_salida }}</td>
                    <td>${{ number_format($reserva->total, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $reserva->estado_pago == 'pagado' ? 'success' : ($reserva->estado_pago == 'cancelado' ? 'danger' : 'warning') }}">
                            {{ ucfirst($reserva->estado_pago) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $reserva->estado_reserva == 'confirmada' ? 'success' : 'danger' }}">
                            {{ ucfirst($reserva->estado_reserva) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar reserva?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No hay reservas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>