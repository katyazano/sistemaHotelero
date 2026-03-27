@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Panel de Gestión de Habitaciones</h1>
        
        @can('create', App\Models\Habitacion::class)
            <a href="{{ route('habitaciones.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Registrar Nueva Habitación
            </a>
        @endcan
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Imagen</th> <th>Número</th>
                        <th>Tipo</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($habitaciones as $habitacion)
                    <tr>
                        <td>
                            @if($habitacion->imagen_url)
                                <img src="{{ asset('storage/' . $habitacion->imagen_url) }}" width="60" class="rounded">
                            @else
                                <span class="text-muted">Sin foto</span>
                            @endif
                        </td>
                        <td>{{ $habitacion->numero }}</td>
                        <td>{{ $habitacion->tipo }}</td>
                        <td>${{ number_format($habitacion->precio, 2) }}</td>
                        <td>
                            <span class="badge {{ $habitacion->estado == 'Disponible' ? 'bg-success' : 'bg-warning' }}">
                                {{ $habitacion->estado }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('habitaciones.show', $habitacion) }}" class="btn btn-sm btn-outline-info">Ver</a>

                            @can('update', $habitacion)
                                <a href="{{ route('habitaciones.edit', $habitacion) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            @endcan

                            @can('delete', $habitacion)
                                <form action="{{ route('habitaciones.destroy', $habitacion) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Seguro?')">Borrar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection