<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema Hotelero</title>
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 text-center">
        <h1 class="display-4 fw-bold">Nuestras Habitaciones</h1>
        <p class="lead text-muted">Encuentra el espacio perfecto para tu descanso.</p>
    </div>

    <div class="container mt-4 mb-5">
        <div class="row">
            @foreach($habitaciones as $habitacion)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="card-title text-primary mb-0">{{ $habitacion->tipo }}</h5>
                            <span class="badge bg-{{ $habitacion->estado == 'disponible' ? 'success' : ($habitacion->estado == 'ocupada' ? 'danger' : 'warning') }}">
                                {{ ucfirst($habitacion->estado) }}
                            </span>
                        </div>
                        <h6 class="card-subtitle mb-3 text-muted">Habitación #{{ $habitacion->numero }}</h6>
                        <p class="card-text">
                            <strong>Capacidad:</strong> {{ $habitacion->capacidad }} personas<br>
                            <strong>Precio:</strong> ${{ number_format($habitacion->precio, 2) }} MXN / noche
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <button class="btn btn-outline-primary w-100" {{ $habitacion->estado != 'disponible' ? 'disabled' : '' }}>
                            Reservar Ahora
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
    </div>

</body>
</html>