@extends('layouts.app')

@section('title', 'Gestionar Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-calendar-check"></i> Gestión de Reservas</h1>
    <a href="{{ route('reservas.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus"></i> Nueva Reserva
    </a>
</div>

<div class="card shadow-sm border-0">
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
                        <span class="badge bg-{{ $reserva->estado_pago === 'pagado' ? 'success' : ($reserva->estado_pago === 'cancelado' ? 'danger' : 'warning text-dark') }}">
                            {{ ucfirst($reserva->estado_pago) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $reserva->estado_reserva === 'confirmada' ? 'success' : 'danger' }}">
                            {{ ucfirst($reserva->estado_reserva) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('reservas.destroy', $reserva) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar esta reserva definitivamente?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
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
@endsection
