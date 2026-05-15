@extends('layouts.app')

@section('title', 'Check-in / Check-out')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-clipboard2-check"></i> Operación de Recepción</h2>
        <p class="text-muted mb-0">Gestiona check-in y check-out del día</p>
    </div>
    <form method="GET" class="d-flex" style="max-width: 320px;">
        <input type="search" name="q" value="{{ $busqueda }}"
               class="form-control me-2" placeholder="Buscar por folio, nombre o email…">
        <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

{{-- Llegadas de hoy --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <span><i class="bi bi-box-arrow-in-right"></i> Llegadas pendientes ({{ $llegadasHoy->count() }})</span>
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
                @forelse($llegadasHoy as $reserva)
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
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin llegadas pendientes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Huéspedes alojados --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info text-white d-flex justify-content-between">
        <span><i class="bi bi-person-badge"></i> Huéspedes alojados ({{ $alojados->count() }})</span>
        <span>Resaltados los que deben salir hoy</span>
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
                @forelse($alojados as $reserva)
                    @php $debeSalirHoy = $reserva->fecha_salida->isToday() || $reserva->fecha_salida->isPast(); @endphp
                    <tr class="{{ $debeSalirHoy ? 'table-warning' : '' }}">
                        <td><span class="fw-bold text-primary">{{ $reserva->folio }}</span></td>
                        <td>
                            {{ $reserva->usuario->nombre ?? '—' }}<br>
                            <small class="text-muted">{{ $reserva->usuario->email ?? '' }}</small>
                        </td>
                        <td>{{ optional($reserva->check_in_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $reserva->fecha_salida->format('d/m/Y') }}</td>
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
                    <tr><td colspan="6" class="text-center text-muted py-4">Sin huéspedes alojados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($salidasHoy->isNotEmpty())
    <div class="alert alert-warning">
        <i class="bi bi-bell"></i>
        <strong>{{ $salidasHoy->count() }}</strong> reserva(s) tienen check-out vencido o programado para hoy.
    </div>
@endif
@endsection
