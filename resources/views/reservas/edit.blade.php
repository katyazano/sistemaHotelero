@extends('layouts.app')

@section('title', 'Editar Reserva')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-pencil-square"></i> Editar Reserva: {{ $reserva->folio }}</h1>
    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form action="{{ route('reservas.update', $reserva) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="id_usuario" class="form-label fw-semibold">Cliente</label>
                    <select class="form-select @error('id_usuario') is-invalid @enderror" id="id_usuario" name="id_usuario" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id_usuario }}"
                                {{ old('id_usuario', $reserva->id_usuario) == $usuario->id_usuario ? 'selected' : '' }}>
                                {{ $usuario->nombre }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_entrada" class="form-label fw-semibold">Fecha Entrada</label>
                    <input type="date" class="form-control @error('fecha_entrada') is-invalid @enderror"
                           id="fecha_entrada" name="fecha_entrada"
                           value="{{ old('fecha_entrada', $reserva->fecha_entrada) }}" required>
                    @error('fecha_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="fecha_salida" class="form-label fw-semibold">Fecha Salida</label>
                    <input type="date" class="form-control @error('fecha_salida') is-invalid @enderror"
                           id="fecha_salida" name="fecha_salida"
                           value="{{ old('fecha_salida', $reserva->fecha_salida) }}" required>
                    @error('fecha_salida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="estado_pago" class="form-label fw-semibold">Estado de Pago</label>
                    <select class="form-select @error('estado_pago') is-invalid @enderror" id="estado_pago" name="estado_pago" required>
                        <option value="pendiente" {{ old('estado_pago', $reserva->estado_pago) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="pagado"    {{ old('estado_pago', $reserva->estado_pago) === 'pagado'    ? 'selected' : '' }}>Pagado</option>
                        <option value="cancelado" {{ old('estado_pago', $reserva->estado_pago) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('estado_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label for="estado_reserva" class="form-label fw-semibold">Estado de Reserva</label>
                    <select class="form-select @error('estado_reserva') is-invalid @enderror" id="estado_reserva" name="estado_reserva" required>
                        <option value="confirmada" {{ old('estado_reserva', $reserva->estado_reserva) === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="cancelada"  {{ old('estado_reserva', $reserva->estado_reserva) === 'cancelada'  ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('estado_reserva') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <h5 class="mt-2 mb-3">Habitaciones</h5>
            <div id="detalles-container">
                @foreach($reserva->detalles as $index => $detalle)
                <div class="row detalle-item mb-2 align-items-center">
                    <input type="hidden" name="detalles[{{ $index }}][id_detalle]" value="{{ $detalle->id_detalle }}">
                    <div class="col-md-6">
                        <select name="detalles[{{ $index }}][id_habitacion]" class="form-select" required>
                            <option value="">Seleccione habitación</option>
                            @foreach($habitaciones as $habitacion)
                                <option value="{{ $habitacion->id_habitacion }}"
                                    {{ $detalle->id_habitacion == $habitacion->id_habitacion ? 'selected' : '' }}>
                                    #{{ $habitacion->numero }} - {{ $habitacion->tipo }} (Cap.{{ $habitacion->capacidad }}) - ${{ $habitacion->precio }}/noche
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="detalles[{{ $index }}][cantidad_personas]"
                               class="form-control" placeholder="Personas" min="1"
                               value="{{ $detalle->cantidad_personas }}" required>
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
                    @if($reserva->imagen_url)
                        <label class="form-label fw-semibold">Imagen actual</label><br>
                        <img src="{{ $reserva->imagen_url }}" class="img-thumbnail mb-2" style="max-height:120px;">
                    @endif
                    <label for="imagen" class="form-label fw-semibold">Cambiar imagen</label>
                    <input type="file" class="form-control @error('imagen') is-invalid @enderror"
                           id="imagen" name="imagen" accept="image/*">
                    @error('imagen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="imagen_url" class="form-label fw-semibold">O URL de imagen externa</label>
                    <input type="url" class="form-control @error('imagen_url') is-invalid @enderror"
                           id="imagen_url" name="imagen_url"
                           value="{{ old('imagen_url', $reserva->imagen_url) }}"
                           placeholder="https://ejemplo.com/imagen.jpg">
                    @error('imagen_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Actualizar Reserva
                </button>
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const habitacionesOptions = `@foreach($habitaciones as $h)<option value="{{ $h->id_habitacion }}">#{{ $h->numero }} - {{ $h->tipo }} (Cap.{{ $h->capacidad }}) - ${{ $h->precio }}/noche</option>@endforeach`;
    let detalleIndex = {{ $reserva->detalles->count() }};

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
