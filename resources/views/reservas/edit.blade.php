<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reserva - Sistema Hotelero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Sistema Hotelero</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/contacto">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reservas.index') }}">Reservas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="display-4 fw-bold text-center">Editar Reserva: {{ $reserva->folio }}</h1>
        <form action="{{ route('reservas.update', $reserva) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_usuario" class="form-label">Cliente</label>
                        <select class="form-control @error('id_usuario') is-invalid @enderror" id="id_usuario" name="id_usuario" required>
                            <option value="">Seleccione un cliente</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id_usuario }}" {{ old('id_usuario', $reserva->id_usuario) == $usuario->id_usuario ? 'selected' : '' }}>
                                    {{ $usuario->nombre }} ({{ $usuario->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="fecha_entrada" class="form-label">Fecha Entrada</label>
                        <input type="date" class="form-control @error('fecha_entrada') is-invalid @enderror" id="fecha_entrada" name="fecha_entrada" value="{{ old('fecha_entrada', $reserva->fecha_entrada) }}" required>
                        @error('fecha_entrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="fecha_salida" class="form-label">Fecha Salida</label>
                        <input type="date" class="form-control @error('fecha_salida') is-invalid @enderror" id="fecha_salida" name="fecha_salida" value="{{ old('fecha_salida', $reserva->fecha_salida) }}" required>
                        @error('fecha_salida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="estado_pago" class="form-label">Estado de Pago</label>
                        <select class="form-control @error('estado_pago') is-invalid @enderror" id="estado_pago" name="estado_pago" required>
                            <option value="pendiente" {{ old('estado_pago', $reserva->estado_pago) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ old('estado_pago', $reserva->estado_pago) == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="cancelado" {{ old('estado_pago', $reserva->estado_pago) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="estado_reserva" class="form-label">Estado de Reserva</label>
                        <select class="form-control @error('estado_reserva') is-invalid @enderror" id="estado_reserva" name="estado_reserva" required>
                            <option value="confirmada" {{ old('estado_reserva', $reserva->estado_reserva) == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="cancelada" {{ old('estado_reserva', $reserva->estado_reserva) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                        @error('estado_reserva') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <h4>Habitaciones</h4>
            <div id="detalles-container">
                @foreach($reserva->detalles as $index => $detalle)
                    <div class="row detalle-item mb-2">
                        <input type="hidden" name="detalles[{{ $index }}][id_detalle]" value="{{ $detalle->id_detalle }}">
                        <div class="col-md-5">
                            <select name="detalles[{{ $index }}][id_habitacion]" class="form-control" required>
                                <option value="">Seleccione habitación</option>
                                @foreach($habitaciones as $habitacion)
                                    <option value="{{ $habitacion->id_habitacion }}" {{ $detalle->id_habitacion == $habitacion->id_habitacion ? 'selected' : '' }}>
                                        #{{ $habitacion->numero }} - {{ $habitacion->tipo }} (Cap.{{ $habitacion->capacidad }}) - ${{ $habitacion->precio }}/noche
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="detalles[{{ $index }}][cantidad_personas]" class="form-control" placeholder="Personas" min="1" value="{{ $detalle->cantidad_personas }}" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-detalle">Eliminar</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" id="add-detalle" class="btn btn-secondary mb-3">Agregar otra habitación</button>

            <div>
                <button type="submit" class="btn btn-primary">Actualizar Reserva</button>
                <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        let detalleIndex = {{ $reserva->detalles->count() }};
        document.getElementById('add-detalle')?.addEventListener('click', function() {
            const container = document.getElementById('detalles-container');
            const newRow = document.createElement('div');
            newRow.className = 'row detalle-item mb-2';
            newRow.innerHTML = `
                <div class="col-md-5">
                    <select name="detalles[${detalleIndex}][id_habitacion]" class="form-control" required>
                        <option value="">Seleccione habitación</option>
                        @foreach($habitaciones as $habitacion)
                            <option value="{{ $habitacion->id_habitacion }}">
                                #{{ $habitacion->numero }} - {{ $habitacion->tipo }} (Cap.{{ $habitacion->capacidad }}) - ${{ $habitacion->precio }}/noche
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="detalles[${detalleIndex}][cantidad_personas]" class="form-control" placeholder="Personas" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-detalle">Eliminar</button>
                </div>
            `;
            container.appendChild(newRow);
            detalleIndex++;
        });

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-detalle')) {
                e.target.closest('.detalle-item').remove();
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>