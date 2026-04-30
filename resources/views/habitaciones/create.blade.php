@extends('layouts.app')

@section('title', 'Nueva Habitación')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Nueva Habitación</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('habitaciones.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        {{-- Número --}}
                        <div class="col-md-6 mb-3">
                            <label for="numero" class="form-label">
                                <i class="bi bi-hash"></i> Número de Habitación
                            </label>
                            <input type="text" name="numero" id="numero" required
                                   class="form-control @error('numero') is-invalid @enderror"
                                   value="{{ old('numero') }}"
                                   placeholder="101">
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipo --}}
                        <div class="col-md-6 mb-3">
                            <label for="tipo" class="form-label">
                                <i class="bi bi-tag"></i> Tipo
                            </label>
                            <select name="tipo" id="tipo" required
                                    class="form-select @error('tipo') is-invalid @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="Sencilla" {{ old('tipo') == 'Sencilla' ? 'selected' : '' }}>Sencilla</option>
                                <option value="Doble" {{ old('tipo') == 'Doble' ? 'selected' : '' }}>Doble</option>
                                <option value="Suite" {{ old('tipo') == 'Suite' ? 'selected' : '' }}>Suite</option>
                                <option value="Penthouse" {{ old('tipo') == 'Penthouse' ? 'selected' : '' }}>Penthouse</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Precio --}}
                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">
                                <i class="bi bi-cash"></i> Precio por Noche
                            </label>
                            <input type="number" name="precio" id="precio" required step="0.01" min="0"
                                   class="form-control @error('precio') is-invalid @enderror"
                                   value="{{ old('precio') }}"
                                   placeholder="1500.00">
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Capacidad --}}
                        <div class="col-md-4 mb-3">
                            <label for="capacidad" class="form-label">
                                <i class="bi bi-people"></i> Capacidad
                            </label>
                            <input type="number" name="capacidad" id="capacidad" required min="1"
                                   class="form-control @error('capacidad') is-invalid @enderror"
                                   value="{{ old('capacidad') }}"
                                   placeholder="2">
                            @error('capacidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="col-md-4 mb-3">
                            <label for="estado" class="form-label">
                                <i class="bi bi-info-circle"></i> Estado
                            </label>
                            <select name="estado" id="estado" required
                                    class="form-select @error('estado') is-invalid @enderror">
                                <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="ocupada" {{ old('estado') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                                <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Imagen --}}
                    <div class="mb-3">
                        <label for="imagen" class="form-label">
                            <i class="bi bi-image"></i> Imagen
                        </label>
                        <input type="file" name="imagen" id="imagen" accept="image/*"
                               class="form-control @error('imagen') is-invalid @enderror">
                        <small class="text-muted">O ingresa una URL de imagen:</small>
                        <input type="url" name="imagen_url" class="form-control mt-2" 
                               placeholder="https://ejemplo.com/imagen.jpg"
                               value="{{ old('imagen_url') }}">
                        @error('imagen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="d-flex gap-2">
                        <a href="{{ route('habitaciones.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-check-circle"></i> Guardar Habitación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
