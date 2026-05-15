@extends('layouts.app')

@section('title', 'Panel de Personal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-people-fill"></i> Panel de Recepción</h2>
        <p class="text-muted mb-0">Bienvenido, {{ auth()->user()->name }}</p>
    </div>
</div>

{{-- Contadores --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
                <h3 class="fw-bold mt-2">{{ $llegadasHoy }}</h3>
                <p class="text-muted mb-0">Llegadas hoy</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-box-arrow-right fs-1 text-warning"></i>
                <h3 class="fw-bold mt-2">{{ $salidasHoy }}</h3>
                <p class="text-muted mb-0">Salidas pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-person-badge fs-1 text-info"></i>
                <h3 class="fw-bold mt-2">{{ $alojadosActuales }}</h3>
                <p class="text-muted mb-0">Alojados actualmente</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body">
                <i class="bi bi-droplet fs-1 text-secondary"></i>
                <h3 class="fw-bold mt-2">{{ $habitacionesLimpieza }}</h3>
                <p class="text-muted mb-0">En limpieza</p>
            </div>
        </div>
    </div>
</div>

{{-- Llegadas pendientes hoy --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-box-arrow-in-right"></i> Llegadas pendientes hoy ({{ $llegadasList->count() }})</span>
        <a href="{{ route('checkinout.index') }}" class="btn btn-sm btn-light">Ver todo</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Huésped</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Habitaciones</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($llegadasList as $reserva)
                <tr>
                    <td><span class="fw-bold text-primary">{{ $reserva->folio }}</span></td>
                    <td>
                        {{ $reserva->usuario->nombre ?? '—' }}<br>
                        <small class="text-muted">{{ $reserva->usuario->email ?? '' }}</small>
                    </td>
                    <td>{{ $reserva->fecha_entrada->format('d/m/Y') }}</td>
                    <td>{{ $reserva->fecha_salida->format('d/m/Y') }}</td>
                    <td>
                        @foreach($reserva->detalles as $d)
                            <span class="badge bg-secondary">#{{ $d->habitacion->numero ?? '?' }}</span>
                        @endforeach
                    </td>
                    <td class="text-end">
                        <form method="POST" action="{{ route('checkinout.checkin', $reserva) }}" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                <i class="bi bi-check2-circle"></i> Check-in
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Sin llegadas pendientes hoy.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Huéspedes alojados --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-badge"></i> Huéspedes alojados ({{ $alojadosList->count() }})</span>
        <span class="small opacity-75">Resaltados en amarillo = deben salir hoy</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Huésped</th>
                    <th>Check-in</th>
                    <th>Salida programada</th>
                    <th>Habitaciones</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alojadosList as $reserva)
                    @php $debeSalirHoy = $reserva->fecha_salida->isToday() || $reserva->fecha_salida->isPast(); @endphp
                    <tr class="{{ $debeSalirHoy ? 'table-warning' : '' }}">
                        <td><span class="fw-bold text-primary">{{ $reserva->folio }}</span></td>
                        <td>
                            {{ $reserva->usuario->nombre ?? '—' }}<br>
                            <small class="text-muted">{{ $reserva->usuario->email ?? '' }}</small>
                        </td>
                        <td>{{ optional($reserva->check_in_at)->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>
                            {{ $reserva->fecha_salida->format('d/m/Y') }}
                            @if($debeSalirHoy)
                                <span class="badge bg-warning text-dark ms-1">Hoy</span>
                            @endif
                        </td>
                        <td>
                            @foreach($reserva->detalles as $d)
                                <span class="badge bg-secondary">#{{ $d->habitacion->numero ?? '?' }}</span>
                            @endforeach
                        </td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('checkinout.checkout', $reserva) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-warning btn-sm">
                                    <i class="bi bi-box-arrow-right"></i> Check-out
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-3">Sin huéspedes alojados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($habitacionesOcupadas > 0)
    <div class="alert alert-light border">
        <i class="bi bi-info-circle"></i>
        Hay <strong>{{ $habitacionesOcupadas }}</strong> habitación(es) marcadas como ocupadas.
    </div>
@endif
@endsection
