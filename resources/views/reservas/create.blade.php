@extends('layouts.app')

@section('title', 'Nueva Reserva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-calendar-plus"></i> Nueva Reserva</h1>
    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('reservas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_usuario" class="form-label fw-semibold">Cliente</label>
                    <select class="form-select @error('id_usuario') is-invalid @enderror" id="id_usuario" name="id_usuario" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id_usuario }}" {{ old('id_usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                {{ $usuario->nombre }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_entrada" class="form-label fw-semibold">Fecha Entrada</label>
                    <input type="date" class="form-control @error('fecha_entrada') is-invalid @enderror"
                           id="fecha_entrada" name="fecha_entrada" value="{{ old('fecha_entrada') }}" required>
                    @error('fecha_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_salida" class="form-label fw-semibold">Fecha Salida</label>
                    <input type="date" class="form-control @error('fecha_salida') is-invalid @enderror"
                           id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida') }}" required>
                    @error('fecha_salida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <h5 class="mt-2 mb-3">Habitaciones</h5>
            <div id="detalles-container">
                @php $oldDetalles = old('detalles', [[]]); @endphp
                @foreach($oldDetalles as $index => $detalle)
                <div class="row detalle-item mb-2 align-items-center">
                    <div class="col-md-6">
                        <select name="detalles[{{ $index }}][id_habitacion]" class="form-select" required>
                            <option value="">Seleccione habitación</option>
                            @foreach($habitaciones as $habitacion)
                                <option value="{{ $habitacion->id_habitacion }}"
                                    {{ ($detalle['id_habitacion'] ?? '') == $habitacion->id_habitacion ? 'selected' : '' }}>
                                    #{{ $habitacion->numero }} - {{ $habitacion->tipo }} (Cap.{{ $habitacion->capacidad }}) - ${{ $habitacion->precio }}/noche
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="detalles[{{ $index }}][cantidad_personas]"
                               class="form-control" placeholder="Personas" min="1"
                               value="{{ $detalle['cantidad_personas'] ?? '' }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger remove-detalle">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" id="add-detalle" class="btn btn-outline-secondary btn-sm mb-4">
                <i class="bi bi-plus-circle"></i> Agregar habitación
            </button>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="imagen" class="form-label fw-semibold">Subir Comprobante/Imagen</label>
                    <input type="file" class="form-control @error('imagen') is-invalid @enderror"
                           id="imagen" name="imagen" accept="image/*">
                    @error('imagen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="imagen_url" class="form-label fw-semibold">O URL de imagen externa</label>
                    <input type="url" class="form-control @error('imagen_url') is-invalid @enderror"
                           id="imagen_url" name="imagen_url" value="{{ old('imagen_url') }}"
                           placeholder="https://ejemplo.com/imagen.jpg">
                    @error('imagen_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Reserva
                </button>
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Build options HTML from PHP data
    const habitacionesOptions = `@foreach($habitaciones as $h)<option value="{{ $h->id_habitacion }}">#{{ $h->numero }} - {{ $h->tipo }} (Cap.{{ $h->capacidad }}) - ${{ $h->precio }}/noche</option>@endforeach`;

    let detalleIndex = {{ count(old('detalles', [[]])) }};

    document.getElementById('add-detalle').addEventListener('click', function () {
        const container = document.getElementById('detalles-container');
        const row = document.createElement('div');
        row.className = 'row detalle-item mb-2 align-items-center';
        row.innerHTML = `
            <div class="col-md-6">
                <select name="detalles[${detalleIndex}][id_habitacion]" class="form-select" required>
                    <option value="">Seleccione habitación</option>
                    ${habitacionesOptions}
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="detalles[${detalleIndex}][cantidad_personas]"
                       class="form-control" placeholder="Personas" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger remove-detalle">
                    <i class="bi bi-trash"></i>
                </button>
            </div>`;
        container.appendChild(row);
        detalleIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-detalle')) {
            e.target.closest('.detalle-item').remove();
        }
    });
</script>
@endpush
