<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Hotelero')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark py-2">
        <div class="container d-flex justify-content-between align-items-center flex-nowrap">
            {{-- Logo --}}
            <a class="navbar-brand fw-bold me-3" href="/">
                <i class="bi bi-building"></i> Sistema Hotelero
            </a>

            {{-- Links de navegación según rol --}}
            @auth
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    @if(auth()->user()->rol === 'admin')
                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('habitaciones.index') }}">
                            <i class="bi bi-door-open"></i> Habitaciones
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('reservas.index') }}">
                            <i class="bi bi-calendar-check"></i> Reservas
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('checkinout.index') }}">
                            <i class="bi bi-clipboard2-check"></i> Check-in/out
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('admin.reportes.pdf') }}">
                            <i class="bi bi-file-earmark-pdf"></i> Reportes
                        </a>
                    @elseif(auth()->user()->rol === 'personal')
                        <a class="btn btn-outline-light btn-sm" href="{{ route('personal.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Operación
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('checkinout.index') }}">
                            <i class="bi bi-clipboard2-check"></i> Check-in/out
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('habitaciones.public') }}">
                            <i class="bi bi-door-open"></i> Catálogo
                        </a>
                    @else
                        <a class="btn btn-outline-light btn-sm" href="{{ route('guest.dashboard') }}">
                            <i class="bi bi-house"></i> Inicio
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('habitaciones.public') }}">
                            <i class="bi bi-door-open"></i> Habitaciones
                        </a>
                        <a class="btn btn-outline-light btn-sm" href="{{ route('mis-reservas') }}">
                            <i class="bi bi-calendar2-heart"></i> Mis Reservas
                        </a>
                    @endif

                    {{-- Cuenta y Salir --}}
                    <span class="text-white-50 small ms-2 d-none d-md-inline">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        <span class="badge bg-secondary">{{ ucfirst(auth()->user()->rol) }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm ms-1">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </button>
                    </form>
                </div>
            @else
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </div>
            @endauth
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
