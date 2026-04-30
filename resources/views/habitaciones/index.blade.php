@extends('layouts.app')

@section('title', 'Gestionar Habitaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-door-open"></i> Gestión de Habitaciones</h1>
    <a href="{{ route('habitaciones.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nueva Habitación
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Imagen</th>
                    <th>Número</th>
                    <th>Tipo</th>
                    <th>Precio/noche</th>
                    <th>Capacidad</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($habitaciones as $habitacion)
                <tr>
                    <td>
                        @if($habitacion->imagen_url)
                            @php
                                $img = $habitacion->imagen_url;
                                if (!str_starts_with($img, 'http') && !str_starts_with($img, '/storage')) {
                                    $img = asset('storage/' . ltrim($img, '/'));
                                }
                            @endphp
                            <img src="{{ $img }}" width="60" class="rounded">
                        @else
                            <span class="text-muted">Sin foto</span>
                        @endif
                    </td>
                    <td>{{ $habitacion->numero }}</td>
                    <td>{{ $habitacion->tipo }}</td>
                    <td>${{ number_format($habitacion->precio, 2) }}</td>
                    <td>{{ $habitacion->capacidad }} personas</td>
                    <td>
                        <span class="badge bg-{{ strtolower($habitacion->estado) === 'disponible' ? 'success' : (strtolower($habitacion->estado) === 'ocupada' ? 'danger' : 'warning text-dark') }}">
                            {{ $habitacion->estado }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('habitaciones.edit', $habitacion->id_habitacion) }}" 
                               class="btn btn-outline-primary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('habitaciones.destroy', $habitacion->id_habitacion) }}" 
                                  class="d-inline" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar esta habitación?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay habitaciones registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
