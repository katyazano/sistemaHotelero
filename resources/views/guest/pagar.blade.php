@extends('layouts.app')

@section('title', 'Confirmar Pago')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-credit-card"></i> Confirmar Pago</h2>
            <a href="{{ route('mis-reservas') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        {{-- Resumen de la reserva --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <i class="bi bi-receipt"></i> Resumen de reserva
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small">Folio</p>
                        <p class="fw-bold fs-5">{{ $reserva->folio }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small">A nombre de</p>
                        <p class="fw-bold">{{ $reserva->usuario->nombre }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small">Fechas</p>
                        <p class="fw-semibold">
                            {{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}
                            &rarr;
                            {{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <p class="mb-1 text-muted small">Habitación(es)</p>
                        <p>
                            @foreach($reserva->detalles as $d)
                                <span class="badge bg-secondary">#{{ $d->habitacion->numero ?? '?' }} {{ $d->habitacion->tipo ?? '' }}</span>
                            @endforeach
                        </p>
                    </div>
                    <div class="col-12">
                        <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-cash"></i> Total a pagar</span>
                            <span class="fs-4 fw-bold">${{ number_format($reserva->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulario de pago --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-wallet2"></i> Datos de pago
            </div>
            <div class="card-body">
                <form action="{{ route('reservas.pagar', $reserva) }}" method="POST" id="pagoForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del titular <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_titular" class="form-control @error('nombre_titular') is-invalid @enderror"
                               value="{{ old('nombre_titular', $reserva->usuario->nombre) }}"
                               placeholder="Nombre completo del titular" required>
                        @error('nombre_titular') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tipo de pago <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="tipo_pago" id="efectivo" value="efectivo"
                                       {{ old('tipo_pago') === 'efectivo' ? 'checked' : '' }} required>
                                <label class="btn btn-outline-secondary w-100 h-100 py-3" for="efectivo">
                                    <i class="bi bi-cash-coin d-block fs-3 mb-1"></i>
                                    Efectivo
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="tipo_pago" id="tarjeta_credito" value="tarjeta_credito"
                                       {{ old('tipo_pago') === 'tarjeta_credito' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary w-100 h-100 py-3" for="tarjeta_credito">
                                    <i class="bi bi-credit-card d-block fs-3 mb-1"></i>
                                    Tarjeta Crédito
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="tipo_pago" id="tarjeta_debito" value="tarjeta_debito"
                                       {{ old('tipo_pago') === 'tarjeta_debito' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary w-100 h-100 py-3" for="tarjeta_debito">
                                    <i class="bi bi-credit-card-2-front d-block fs-3 mb-1"></i>
                                    Tarjeta Débito
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="radio" class="btn-check" name="tipo_pago" id="transferencia" value="transferencia"
                                       {{ old('tipo_pago') === 'transferencia' ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary w-100 h-100 py-3" for="transferencia">
                                    <i class="bi bi-bank d-block fs-3 mb-1"></i>
                                    Transferencia
                                </label>
                            </div>
                        </div>
                        @error('tipo_pago') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Datos de tarjeta (solo si es tarjeta) --}}
                    <div id="datosTarjeta" class="d-none">
                        <hr>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-shield-lock"></i>
                            Esta es una simulación de pago. No ingreses datos reales de tarjeta.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Número de tarjeta</label>
                            <input type="text" name="numero_tarjeta"
                                   class="form-control @error('numero_tarjeta') is-invalid @enderror"
                                   placeholder="0000 0000 0000 0000" maxlength="19"
                                   value="{{ old('numero_tarjeta') }}" id="numTarjeta">
                            @error('numero_tarjeta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vencimiento (MM/AA)</label>
                                <input type="text" name="expiracion"
                                       class="form-control @error('expiracion') is-invalid @enderror"
                                       placeholder="MM/AA" maxlength="5"
                                       value="{{ old('expiracion') }}" id="expiracion">
                                @error('expiracion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">CVV</label>
                                <input type="text" name="cvv"
                                       class="form-control @error('cvv') is-invalid @enderror"
                                       placeholder="123" maxlength="4"
                                       value="{{ old('cvv') }}">
                                @error('cvv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i>
                            Confirmar Pago de ${{ number_format($reserva->total, 2) }}
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
    const radios = document.querySelectorAll('input[name="tipo_pago"]');
    const datosTarjeta = document.getElementById('datosTarjeta');

    function toggleTarjeta() {
        const val = document.querySelector('input[name="tipo_pago"]:checked')?.value;
        const esTarjeta = val === 'tarjeta_credito' || val === 'tarjeta_debito';
        datosTarjeta.classList.toggle('d-none', !esTarjeta);
    }

    radios.forEach(r => r.addEventListener('change', toggleTarjeta));
    toggleTarjeta();

    // Formatear número de tarjeta con espacios
    document.getElementById('numTarjeta')?.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').substring(0, 16);
        this.value = v.replace(/(.{4})/g, '$1 ').trim();
    });

    // Formatear vencimiento MM/AA
    document.getElementById('expiracion')?.addEventListener('input', function () {
        let v = this.value.replace(/\D/g, '').substring(0, 4);
        if (v.length >= 3) v = v.slice(0,2) + '/' + v.slice(2);
        this.value = v;
    });
</script>
@endpush
