@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-speedometer2"></i> Panel de Administración</h1>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 small">Total Reservas</p>
                        <h3 class="fw-bold">{{ $totalReservas }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 small">Habitaciones</p>
                        <h3 class="fw-bold">{{ $totalHabitaciones }}</h3>
                    </div>
                    <i class="bi bi-door-open fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 small">Huéspedes</p>
                        <h3 class="fw-bold">{{ $totalUsuarios }}</h3>
                    </div>
                    <i class="bi bi-people fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-1 small">Ingresos Totales</p>
                        <h3 class="fw-bold">${{ number_format($ingresosTotales, 0) }}</h3>
                    </div>
                    <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history"></i> Reservas Recientes</span>
        <a href="{{ route('reservas.index') }}" class="btn btn-sm btn-outline-light">Ver todas</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-secondary">
                <tr>
                    <th>Folio</th><th>Cliente</th><th>Entrada</th><th>Salida</th><th>Total</th><th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservasRecientes as $r)
                <tr>
                    <td class="fw-bold">{{ $r->folio }}</td>
                    <td>{{ $r->usuario->nombre ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->fecha_entrada)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->fecha_salida)->format('d/m/Y') }}</td>
                    <td>${{ number_format($r->total, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $r->estado_reserva === 'confirmada' ? 'success' : 'danger' }}">
                            {{ ucfirst($r->estado_reserva) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Sin reservas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
