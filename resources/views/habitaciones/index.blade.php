@extends('layouts.app')

@section('title', 'Gestionar Habitaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-door-open"></i> Gestión de Habitaciones</h1>
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
                        <span class="badge bg-{{ strtolower($habitacion->estado) === 'disponible' ? 'success' : 'warning text-dark' }}">
                            {{ $habitacion->estado }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="text-muted small">—</span>
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
