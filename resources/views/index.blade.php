<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Habitaciones - Sistema Hotelero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    {{-- Navbar --}}
    <nav class="navbar navbar-dark bg-dark py-2">
        <div class="container d-flex justify-content-between align-items-center flex-nowrap">
            <a class="navbar-brand fw-bold me-3" href="/">
                <i class="bi bi-building"></i> Sistema Hotelero
            </a>
            <div class="d-flex align-items-center gap-2">
                <a class="btn btn-outline-light btn-sm" href="{{ url('/contacto') }}">Contacto</a>
                @auth
                    @if(auth()->user()->rol === 'admin')
                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @elseif(auth()->user()->rol === 'personal')
                        <a class="btn btn-outline-light btn-sm" href="{{ route('personal.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Operación
                        </a>
                    @else
                        <a class="btn btn-outline-light btn-sm" href="{{ route('mis-reservas') }}">
                            <i class="bi bi-calendar2-heart"></i> Mis Reservas
                        </a>
                    @endif
                    <span class="text-white-50 small d-none d-md-inline">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <div class="hero-section bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Encuentra tu habitación ideal</h1>
            <p class="lead">Consulta disponibilidad en tiempo real y reserva en línea</p>
        </div>
    </div>

    {{-- Buscador de disponibilidad --}}
    <div class="container" style="margin-top:-40px;">
        <div class="card shadow border-0">
            <div class="card-body">
                <form method="GET" action="{{ route('habitaciones.public') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="fecha_entrada" class="form-label fw-semibold">
                            <i class="bi bi-calendar-event"></i> Fecha de entrada
                        </label>
                        <input type="date" id="fecha_entrada" name="fecha_entrada"
                               class="form-control" min="{{ $minDate }}"
                               value="{{ $fechaEntrada }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_salida" class="form-label fw-semibold">
                            <i class="bi bi-calendar-check"></i> Fecha de salida
                        </label>
                        <input type="date" id="fecha_salida" name="fecha_salida"
                               class="form-control" min="{{ $minDate }}"
                               value="{{ $fechaSalida }}" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Buscar disponibilidad
                        </button>
                    </div>
                </form>
                @if($errorBusqueda)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="bi bi-exclamation-triangle"></i> {{ $errorBusqueda }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Habitaciones Grid --}}
    <main class="container my-5">
        @if($busquedaActiva)
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Mostrando habitaciones libres del
                <strong>{{ \Carbon\Carbon::parse($fechaEntrada)->format('d/m/Y') }}</strong>
                al
                <strong>{{ \Carbon\Carbon::parse($fechaSalida)->format('d/m/Y') }}</strong>
                ({{ $habitaciones->count() }} resultado{{ $habitaciones->count() === 1 ? '' : 's' }}).
            </div>
        @endif

        <div class="row g-4">
            @forelse($habitaciones as $habitacion)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($habitacion->imagen_url)
                        @php
                            $img = $habitacion->imagen_url;
                            if (!str_starts_with($img, 'http') && !str_starts_with($img, '/storage')) {
                                $img = asset('storage/' . ltrim($img, '/'));
                            }
                        @endphp
                        <img src="{{ $img }}" class="card-img-top" alt="{{ $habitacion->tipo }}" style="height: 250px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 250px;">
                            <i class="bi bi-image fs-1"></i>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">{{ $habitacion->tipo }}</h5>
                            @if($busquedaActiva)
                                <span class="badge bg-success">Disponible</span>
                            @else
                                <span class="badge bg-{{ strtolower($habitacion->estado) === 'disponible' ? 'success' : 'warning text-dark' }}">
                                    {{ ucfirst($habitacion->estado) }}
                                </span>
                            @endif
                        </div>

                        <p class="text-muted small mb-2">
                            <i class="bi bi-door-closed"></i> Habitación #{{ $habitacion->numero }}
                        </p>

                        <ul class="list-unstyled mb-3">
                            <li><i class="bi bi-people"></i> <strong>Capacidad:</strong> {{ $habitacion->capacidad }} personas</li>
                            <li><i class="bi bi-cash"></i> <strong>Precio:</strong> ${{ number_format($habitacion->precio, 2) }} / noche</li>
                            @if($busquedaActiva)
                                @php $noches = \Carbon\Carbon::parse($fechaEntrada)->diffInDays(\Carbon\Carbon::parse($fechaSalida)); @endphp
                                <li class="mt-2 text-success">
                                    <i class="bi bi-calculator"></i>
                                    <strong>Total {{ $noches }} noche{{ $noches === 1 ? '' : 's' }}:</strong>
                                    ${{ number_format($habitacion->precio * $noches, 2) }}
                                </li>
                            @endif
                        </ul>

                        @auth
                            @if(auth()->user()->rol === 'guest')
                                @php
                                    $params = ['habitacion' => $habitacion->id_habitacion];
                                    if ($busquedaActiva) {
                                        $params['fecha_entrada'] = $fechaEntrada;
                                        $params['fecha_salida'] = $fechaSalida;
                                    }
                                @endphp
                                <a href="{{ route('reservas.guest.create', $params) }}" class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-check"></i> Reservar Ahora
                                </a>
                            @else
                                <button class="btn btn-secondary w-100" disabled>
                                    Solo huéspedes pueden reservar
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Inicia sesión para reservar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="bi bi-emoji-frown"></i>
                    @if($busquedaActiva)
                        No hay habitaciones libres para esas fechas. Intenta con otro rango.
                    @else
                        No hay habitaciones registradas en este momento.
                    @endif
                </div>
            </div>
            @endforelse
        </div>
    </main>

    <footer class="footer bg-dark text-white py-3">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Sistema Hotelero. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ajusta dinámicamente la fecha mínima de salida según la entrada
        document.getElementById('fecha_entrada')?.addEventListener('change', function () {
            const salida = document.getElementById('fecha_salida');
            if (salida) salida.min = this.value;
        });
    </script>
</body>
</html>
