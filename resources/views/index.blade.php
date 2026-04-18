<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Sistema Hotelero</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    <div class="container mx-auto px-4 py-12 text-center">
        <h1 class="text-4xl font-bold text-slate-900">Nuestras Habitaciones</h1>
        <p class="mt-4 text-slate-600">Encuentra el espacio perfecto para tu descanso.</p>
    </div>

    <main class="container mx-auto px-4 pb-16">
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach($habitaciones as $habitacion)
            <div>
                <x-card :title="$habitacion->tipo">
                    @if ($habitacion->imagen_url)
                        <img src="{{ $habitacion->imagen_url }}" alt="{{ $habitacion->tipo }}" class="w-full h-48 object-cover rounded-lg mb-4">
                    @else
                        <div class="w-full h-48 bg-slate-200 text-slate-500 rounded-lg flex items-center justify-center mb-4">
                            Sin imagen
                        </div>
                    @endif

                    <div class="space-y-3 text-slate-700">
                        <div class="flex items-center justify-between text-sm text-slate-500">
                            <span>Habitación #{{ $habitacion->numero }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold text-white {{ $habitacion->estado == 'disponible' ? 'bg-emerald-600' : ($habitacion->estado == 'ocupada' ? 'bg-red-600' : 'bg-amber-500') }}">
                                {{ ucfirst($habitacion->estado) }}
                            </span>
                        </div>
                        <p>
                            <strong>Capacidad:</strong> {{ $habitacion->capacidad }} personas<br>
                            <strong>Precio:</strong> ${{ number_format($habitacion->precio, 2) }} MXN / noche
                        </p>
                    </div>

                    <x-slot:footer>
                        <x-button variant="{{ $habitacion->estado == 'disponible' ? 'primary' : 'secondary' }}" class="w-full" :disabled="$habitacion->estado != 'disponible'">
                            Reservar Ahora
                        </x-button>
                    </x-slot:footer>
                </x-card>
            </div>
            @endforeach
        </div>
    </main>

</body>
</html>