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
                        <a class="nav-link active" href="{{ route('reservas.index') }}">Reservas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="display-4 fw-bold text-center mb-4">Listado de Reservas</h1>

        {{-- Mensajes de Retroalimentación --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="text-end mb-3">
            {{-- Todos pueden crear una reserva (Huésped desde su perfil o Personal en recepción) --}}
            <a href="{{ route('reservas.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-calendar-plus"></i> Nueva Reserva
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Cliente</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Total</th>
                            <th>Estado Pago</th>
                            <th>Estado Reserva</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reservas as $reserva)
                        <tr>
                            <td class="fw-bold">{{ $reserva->folio }}</td>
                            <td>
                                {{ $reserva->usuario->nombre ?? 'N/A' }}<br>
                                <small class="text-muted">{{ $reserva->usuario->email ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</td>
                            <td class="fw-bold">${{ number_format($reserva->total, 2) }}</td>
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
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    {{-- CONSULTAR: Todos los roles pueden ver el detalle --}}
                                    <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-sm btn-info text-white">Ver</a>

                                    {{-- EDITAR: Restringido a Admin y Personal (Recepcionista) según la Policy --}}
                                    @can('update', $reserva)
                                        <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-sm btn-warning">Editar</a>
                                    @endcan

                                    {{-- ELIMINAR: Acción exclusiva del Administrador para integridad de datos --}}
                                    @can('delete', $reserva)
                                        <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar esta reserva definitivamente?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No se encontraron reservas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>