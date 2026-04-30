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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="bi bi-building"></i> Sistema Hotelero
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/contacto') }}">Contacto</a>
                    </li>
                    @auth
                        @if(auth()->user()->rol === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('guest.dashboard') }}">Mi Panel</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <div class="bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Nuestras Habitaciones</h1>
            <p class="lead">Encuentra el espacio perfecto para tu descanso</p>
        </div>
    </div>

    {{-- Habitaciones Grid --}}
    <main class="container my-5">
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
                            <span class="badge bg-{{ strtolower($habitacion->estado) === 'disponible' ? 'success' : 'warning text-dark' }}">
                                {{ ucfirst($habitacion->estado) }}
                            </span>
                        </div>
                        
                        <p class="text-muted small mb-2">
                            <i class="bi bi-door-closed"></i> Habitación #{{ $habitacion->numero }}
                        </p>
                        
                        <ul class="list-unstyled mb-3">
                            <li><i class="bi bi-people"></i> <strong>Capacidad:</strong> {{ $habitacion->capacidad }} personas</li>
                            <li><i class="bi bi-cash"></i> <strong>Precio:</strong> ${{ number_format($habitacion->precio, 2) }} / noche</li>
                        </ul>
                        
                        @auth
                            @if(auth()->user()->rol === 'guest')
                                <a href="{{ route('reservas.guest.create', $habitacion->id_habitacion) }}" 
                                   class="btn btn-primary w-100 {{ $habitacion->estado !== 'disponible' ? 'disabled' : '' }}">
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
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No hay habitaciones disponibles en este momento.
                </div>
            </div>
            @endforelse
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">&copy; 2026 Sistema Hotelero. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>