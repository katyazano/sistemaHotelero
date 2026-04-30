@extends('layouts.app')

@section('title', 'Detalle de Reserva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-calendar-event"></i> Reserva: {{ $reserva->folio }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('reservas.edit', $reserva) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white">Información General</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">Cliente</dt>
                    <dd class="col-sm-7">{{ $reserva->usuario->nombre ?? 'N/A' }}</dd>
                    <dt class="col-sm-5">Email</dt>
                    <dd class="col-sm-7">{{ $reserva->usuario->email ?? 'N/A' }}</dd>
                    <dt class="col-sm-5">Fecha Entrada</dt>
                    <dd class="col-sm-7">{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</dd>
                    <dt class="col-sm-5">Fecha Salida</dt>
                    <dd class="col-sm-7">{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</dd>
                    <dt class="col-sm-5">Noches</dt>
                    <dd class="col-sm-7">{{ (strtotime($reserva->fecha_salida) - strtotime($reserva->fecha_entrada)) / 86400 }}</dd>
                    <dt class="col-sm-5">Estado Pago</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $reserva->estado_pago === 'pagado' ? 'success' : ($reserva->estado_pago === 'cancelado' ? 'danger' : 'warning text-dark') }}">
                            {{ ucfirst($reserva->estado_pago) }}
                        </span>
                    </dd>
                    <dt class="col-sm-5">Estado Reserva</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $reserva->estado_reserva === 'confirmada' ? 'success' : 'danger' }}">
                            {{ ucfirst($reserva->estado_reserva) }}
                        </span>
                    </dd>
                    <dt class="col-sm-5">Total</dt>
                    <dd class="col-sm-7 fw-bold fs-5">${{ number_format($reserva->total, 2) }} MXN</dd>
                </dl>
            </div>
        </div>
    </div>

    @if($reserva->imagen_url)
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white">Imagen Adjunta</div>
            <div class="card-body text-center">
                <img src="{{ $reserva->imagen_url }}" alt="Imagen de reserva" class="img-fluid rounded" style="max-height:250px;">
            </div>
        </div>
    </div>
    @endif
</div>

<div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-dark text-white">Habitaciones Reservadas</div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-secondary">
                <tr>
                    <th>Habitación</th><th>Tipo</th><th>Personas</th><th>Precio/noche</th><th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reserva->detalles as $detalle)
                <tr>
                    <td>#{{ $detalle->habitacion->numero ?? 'N/A' }}</td>
                    <td>{{ $detalle->habitacion->tipo ?? 'N/A' }}</td>
                    <td>{{ $detalle->cantidad_personas }}</td>
                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td>${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
