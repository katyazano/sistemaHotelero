<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Hotelero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    {{-- Header --}}
                    <div class="login-header">
                        <i class="bi bi-building fs-1 mb-3"></i>
                        <h2 class="fw-bold mb-2">Bienvenido</h2>
                        <p class="mb-0 opacity-75">Inicia sesión en tu cuenta</p>
                    </div>

                    {{-- Body --}}
                    <div class="login-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope"></i> Correo Electrónico
                                </label>
                                <input type="email" name="email" id="email" required autofocus
                                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="tu@email.com">
                                @error('email')
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
                                       placeholder="••••••••">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Remember & Forgot --}}
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Recordarme</label>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </form>

                        {{-- Register Link --}}
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                ¿No tienes una cuenta?
                                <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">Regístrate aquí</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>