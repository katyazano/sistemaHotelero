@extends('layouts.app')

@section('title', 'Mi Panel')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-house-heart"></i> Bienvenido, {{ auth()->user()->name }}</h1>
    <p class="text-muted">Aquí puedes ver un resumen de tus reservas recientes.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <a href="{{ route('mis-reservas') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-shadow">
                <i class="bi bi-calendar2-heart fs-1 text-primary mb-2"></i>
                <h5 class="fw-bold">Mis Reservas</h5>
                <p class="text-muted small">Consulta y gestiona tus reservas</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('habitaciones.public') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 text-center p-4 hover-shadow">
                <i class="bi bi-door-open fs-1 text-success mb-2"></i>
                <h5 class="fw-bold">Ver Habitaciones</h5>
                <p class="text-muted small">Explora y reserva habitaciones</p>
            </div>
        </a>
    </div>
</div>

@if($misReservas->count())
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white">
        <i class="bi bi-clock-history"></i> Reservas Recientes
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-secondary">
                <tr><th>Folio</th><th>Entrada</th><th>Salida</th><th>Total</th><th>Estado</th></tr>
            </thead>
            <tbody>
                @foreach($misReservas as $r)
                <tr>
                    <td class="fw-bold">{{ $r->folio }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->fecha_entrada)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($r->fecha_salida)->format('d/m/Y') }}</td>
                    <td>${{ number_format($r->total, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $r->estado_reserva === 'confirmada' ? 'success' : 'danger' }}">
                            {{ ucfirst($r->estado_reserva) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
