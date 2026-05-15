@extends('layouts.app')

@section('title', 'Gestionar Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 fw-bold"><i class="bi bi-calendar-check"></i> Gestión de Reservas</h1>
    <a href="{{ route('reservas.create') }}" class="btn btn-primary">
        <i class="bi bi-calendar-plus"></i> Nueva Reserva
    </a>
</div>

{{-- Buscador y filtros --}}
<form method="GET" action="{{ route('reservas.index') }}" class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Buscar por folio, nombre o email…"
                       value="{{ request('q') }}">
            </div>
            <div class="col-md-3">
                <select name="estado_reserva" class="form-select form-select-sm">
                    <option value="">— Estado reserva —</option>
                    @foreach(['pendiente','confirmada','check_in','check_out','cancelada'] as $e)
                        <option value="{{ $e }}" {{ request('estado_reserva') === $e ? 'selected' : '' }}>
                            {{ match($e) {
                                'check_in'  => 'Check-in',
                                'check_out' => 'Check-out',
                                default     => ucfirst($e)
                            } }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="estado_pago" class="form-select form-select-sm">
                    <option value="">— Estado pago —</option>
                    @foreach(['pendiente','pagado','cancelado'] as $e)
                        <option value="{{ $e }}" {{ request('estado_pago') === $e ? 'selected' : '' }}>
                            {{ ucfirst($e) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </div>
    </div>
</form>

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
                    <th class="text-center" onclick="event.stopPropagation()">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $reserva)
                @php
                    $colorEstado = match($reserva->estado_reserva) {
                        'confirmada' => 'success',
                        'check_in'   => 'info',
                        'check_out'  => 'secondary',
                        'cancelada'  => 'danger',
                        default      => 'warning text-dark',
                    };
                    $labelEstado = match($reserva->estado_reserva) {
                        'check_in'  => 'Check-in',
                        'check_out' => 'Check-out',
                        default     => ucfirst($reserva->estado_reserva),
                    };
                    $colorPago = match($reserva->estado_pago) {
                        'pagado'    => 'success',
                        'cancelado' => 'danger',
                        default     => 'warning text-dark',
                    };
                @endphp
                <tr style="cursor:pointer" onclick="window.location.href='{{ route('reservas.edit', $reserva) }}'">
                    <td class="fw-bold">{{ $reserva->folio }}</td>
                    <td>
                        {{ $reserva->usuario->nombre ?? 'N/A' }}<br>
                        <small class="text-muted">{{ $reserva->usuario->email ?? '' }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</td>
                    <td class="fw-bold">${{ number_format($reserva->total, 2) }}</td>
                    <td><span class="badge bg-{{ $colorPago }}">{{ ucfirst($reserva->estado_pago) }}</span></td>
                    <td><span class="badge bg-{{ $colorEstado }}">{{ $labelEstado }}</span></td>
                    <td class="text-center" onclick="event.stopPropagation()">
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
                    <td colspan="8" class="text-center py-4 text-muted">No se encontraron reservas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservas->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Mostrando {{ $reservas->firstItem() }}–{{ $reservas->lastItem() }} de {{ $reservas->total() }} reservas
        </small>
        {{ $reservas->links() }}
    </div>
    @endif
</div>
@endsection
