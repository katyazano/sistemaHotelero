@extends('layouts.app')

@section('title', 'Nueva Reserva')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Nueva Reserva</h4>
            </div>
            <div class="card-body">
                {{-- Información de la habitación --}}
                <div class="alert alert-info">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            @if($habitacion->imagen_url)
                                @php
                                    $img = $habitacion->imagen_url;
                                    if (!str_starts_with($img, 'http') && !str_starts_with($img, '/storage')) {
                                        $img = asset('storage/' . ltrim($img, '/'));
                                    }
                                @endphp
                                <img src="{{ $img }}" class="img-fluid rounded" alt="{{ $habitacion->tipo }}">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h5 class="fw-bold">{{ $habitacion->tipo }}</h5>
                            <p class="mb-1"><i class="bi bi-door-closed"></i> Habitación #{{ $habitacion->numero }}</p>
                            <p class="mb-1"><i class="bi bi-people"></i> Capacidad: {{ $habitacion->capacidad }} personas</p>
                            <p class="mb-0"><i class="bi bi-cash"></i> Precio: <strong>${{ number_format($habitacion->precio, 2) }}</strong> / noche</p>
                        </div>
                    </div>
                </div>

                {{-- Formulario de reserva --}}
                <form method="POST" action="{{ route('reservas.guest.store') }}">
                    @csrf
                    <input type="hidden" name="id_habitacion" value="{{ $habitacion->id_habitacion }}">

                    <div class="row">
                        {{-- Fecha de entrada --}}
                        <div class="col-md-6 mb-3">
                            <label for="fecha_entrada" class="form-label fw-semibold">
                                <i class="bi bi-calendar-check"></i> Fecha de Entrada
                            </label>
                            <input type="date" name="fecha_entrada" id="fecha_entrada" required
                                   class="form-control @error('fecha_entrada') is-invalid @enderror"
                                   value="{{ old('fecha_entrada', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}">
                            @error('fecha_entrada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fecha de salida --}}
                        <div class="col-md-6 mb-3">
                            <label for="fecha_salida" class="form-label fw-semibold">
                                <i class="bi bi-calendar-x"></i> Fecha de Salida
                            </label>
                            <input type="date" name="fecha_salida" id="fecha_salida" required
                                   class="form-control @error('fecha_salida') is-invalid @enderror"
                                   value="{{ old('fecha_salida', date('Y-m-d', strtotime('+1 day'))) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('fecha_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Resumen de costo --}}
                    <div class="alert alert-light border">
                        <h6 class="fw-bold">Resumen de Costo</h6>
                        <div class="d-flex justify-content-between">
                            <span>Precio por noche:</span>
                            <span class="fw-bold">${{ number_format($habitacion->precio, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Número de noches:</span>
                            <span class="fw-bold" id="noches">1</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total estimado:</span>
                            <span class="fw-bold text-primary fs-5" id="total">${{ number_format($habitacion->precio, 2) }}</span>
                        </div>
                    </div>

                    {{-- Información del huésped --}}
                    <div class="alert alert-secondary">
                        <h6 class="fw-bold"><i class="bi bi-person"></i> Información del Huésped</h6>
                        <p class="mb-1"><strong>Nombre:</strong> {{ auth()->user()->name }}</p>
                        <p class="mb-0"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    </div>

                    {{-- Botones --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('habitaciones.public') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle"></i> Confirmar Reserva
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calcular noches y total automáticamente
    const precioNoche = {{ $habitacion->precio }};
    const fechaEntrada = document.getElementById('fecha_entrada');
    const fechaSalida = document.getElementById('fecha_salida');
    const nochesSpan = document.getElementById('noches');
    const totalSpan = document.getElementById('total');

    function calcularTotal() {
        const entrada = new Date(fechaEntrada.value);
        const salida = new Date(fechaSalida.value);
        
        if (entrada && salida && salida > entrada) {
            const diffTime = Math.abs(salida - entrada);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const total = precioNoche * diffDays;
            
            nochesSpan.textContent = diffDays;
            totalSpan.textContent = '$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    fechaEntrada.addEventListener('change', calcularTotal);
    fechaSalida.addEventListener('change', calcularTotal);
    
    // Calcular al cargar
    calcularTotal();
</script>
@endpush$' + total.toLocaleString('es-MX', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    }

    fechaEntrada.addEventListener('change', calcularTotal);
    fechaSalida.addEventListener('change', calcularTotal);
    
    // Calcular al cargar
    calcularTotal();
</script>
@endpush
