<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Hotelero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="auth-card">
                    {{-- Header --}}
                    <div class="auth-header">
                        <i class="bi bi-person-plus-fill"></i>
                        <h2>Crear Cuenta</h2>
                        <p>Comienza a gestionar tus reservas</p>
                    </div>

                    {{-- Body --}}
                    <div class="auth-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            {{-- Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-person"></i> Nombre Completo
                                </label>
                                <input type="text" name="name" id="name" required autofocus
                                       class="form-control form-control-lg @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="Juan Pérez">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" name="email" id="email" required
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="tu@email.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Teléfono --}}
                            <div class="mb-3">
                                <label for="telefono" class="form-label fw-semibold">
                                    <i class="bi bi-telephone"></i> Teléfono
                                </label>
                                <input type="tel" name="telefono" id="telefono"
                                       class="form-control form-control-lg @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono') }}"
                                       placeholder="+52 123 456 7890">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Fecha de Nacimiento --}}
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_nacimiento" class="form-label fw-semibold">
                                        <i class="bi bi-calendar"></i> Fecha de Nacimiento
                                    </label>
                                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                           class="form-control form-control-lg @error('fecha_nacimiento') is-invalid @enderror"
                                           value="{{ old('fecha_nacimiento') }}"
                                           max="{{ date('Y-m-d') }}">
                                    @error('fecha_nacimiento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Country --}}
                                <div class="col-md-6 mb-3">
                                    <label for="pais" class="form-label fw-semibold">
                                        <i class="bi bi-globe"></i> País
                                    </label>
                                    <select name="pais" id="pais"
                                            class="form-select form-select-lg @error('pais') is-invalid @enderror">
                                        <option value="">Cargando países...</option>
                                    </select>
                                    @error('pais')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Dirección --}}
                            <div class="mb-3">
                                <label for="direccion" class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt"></i> Dirección
                                </label>
                                <textarea name="direccion" id="direccion" rows="2"
                                          class="form-control form-control-lg @error('direccion') is-invalid @enderror"
                                          placeholder="Calle, número, colonia, ciudad">{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="bi bi-lock"></i> Contraseña
                                </label>
                                <input type="password" name="password" id="password" required
                                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 6 caracteres">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="bi bi-lock-fill"></i> Confirmar Contraseña
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                       class="form-control form-control-lg"
                                       placeholder="Repite tu contraseña">
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-success btn-auth w-100">
                                <i class="bi bi-check-circle"></i> Registrarse
                            </button>
                        </form>

                        {{-- Login Link --}}
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                ¿Ya tienes una cuenta?
                                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: var(--primary-color);">Inicia Sesión</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fetch countries from REST Countries API
        fetch('https://restcountries.com/v3.1/all?fields=name')
            .then(r => r.json())
            .then(data => {
                const select = document.getElementById('pais');
                const countries = data
                    .map(c => c.name.common)
                    .sort((a, b) => a.localeCompare(b));

                select.innerHTML = '<option value="">Selecciona tu país</option>';
                const selected = "{{ old('pais') }}";
                countries.forEach(name => {
                    const opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    if (name === selected) opt.selected = true;
                    select.appendChild(opt);
                });
            })
            .catch(() => {
                document.getElementById('pais').innerHTML =
                    '<option value="">No se pudo cargar la lista</option>';
            });
    </script>
</body>
</html>
