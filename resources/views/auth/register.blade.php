<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Hotelero</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-light">
    <nav class="bg-gray-800 shadow-sm mb-4">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-3">
                <a class="text-white font-medium text-slate-900" href="/">Sistema Hotelero</a>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-white hover:text-gray-200">Inicio</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-center mb-6 text-slate-900">Registro de Usuario</h1>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('usuarios.store') }}" method="POST" x-data="registroForm()">
                @csrf

                <!-- Nombre -->
                <div class="mb-4">
                    <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <!-- Teléfono -->
                <div class="mb-4">
                    <label for="telefono" class="block text-sm font-medium text-slate-700">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- País -->
                <div class="mb-4">
                    <label for="pais" class="block text-sm font-medium text-slate-700">País</label>
                    <select id="pais" name="pais" @change="cargarPais()" x-model="paisSeleccionado"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">Seleccionar país...</option>
                        <template x-for="pais in paises" :key="pais.cca2">
                            <option :value="pais.name.common" x-text="pais.name.common"></option>
                        </template>
                    </select>
                </div>

                <!-- Bandera del País -->
                <div class="mb-4" x-show="paisData" x-transition>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bandera</label>
                    <div class="flex justify-center p-4 bg-gray-50 border border-gray-200 rounded-md">
                        <img :src="paisData.flags.svg" :alt="'Bandera de ' + paisSeleccionado"
                             class="h-20 w-auto border border-gray-300 rounded shadow-sm">
                    </div>
                </div>

                <!-- Capital -->
                <div class="mb-4" x-show="paisData" x-transition>
                    <label for="capital" class="block text-sm font-medium text-slate-700">Capital</label>
                    <input type="text" id="capital" name="capital" :value="paisData.capital ? paisData.capital[0] : ''"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50"
                        readonly>
                </div>

                <!-- Moneda -->
                <div class="mb-4" x-show="paisData" x-transition>
                    <label for="moneda" class="block text-sm font-medium text-slate-700">Moneda</label>
                    <input type="text" id="moneda" name="moneda" :value="obtenerMoneda()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50"
                        readonly>
                </div>

                <!-- Idiomas -->
                <div class="mb-4" x-show="paisData" x-transition>
                    <label for="idiomas" class="block text-sm font-medium text-slate-700">Idiomas</label>
                    <input type="text" id="idiomas" name="idiomas" :value="obtenerIdiomas()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50"
                        readonly>
                </div>

                <!-- Zona Horaria -->
                <div class="mb-4" x-show="paisData" x-transition>
                    <label for="zona_horaria" class="block text-sm font-medium text-slate-700">Zona Horaria</label>
                    <input type="text" id="zona_horaria" name="zona_horaria" :value="paisData.timezones ? paisData.timezones[0] : ''"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50"
                        readonly>
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-slate-700">Contraseña</label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    Registrarse
                </button>

                <p class="text-center text-sm text-slate-600 mt-4">
                    ¿Ya tienes cuenta? <a href="/" class="text-blue-600 hover:text-blue-800">Inicia sesión</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        function registroForm() {
            return {
                paises: [],
                paisSeleccionado: '{{ old("pais") }}',
                paisData: null,

                async init() {
                    await this.cargarPaises();
                    if (this.paisSeleccionado) {
                        await this.cargarPais();
                    }
                },

                async cargarPaises() {
                    try {
                        const respuesta = await fetch('https://restcountries.com/v3.1/all?fields=name,cca2');
                        this.paises = await respuesta.json();
                        this.paises.sort((a, b) => a.name.common.localeCompare(b.name.common));
                    } catch (error) {
                        console.error('Error cargando países:', error);
                    }
                },

                async cargarPais() {
                    if (!this.paisSeleccionado) {
                        this.paisData = null;
                        return;
                    }

                    try {
                        const respuesta = await fetch(`https://restcountries.com/v3.1/name/${this.paisSeleccionado}?fields=name,capital,currencies,languages,timezones,flags`);
                        const datos = await respuesta.json();
                        this.paisData = datos[0];
                    } catch (error) {
                        console.error('Error cargando datos del país:', error);
                        this.paisData = null;
                    }
                },

                obtenerMoneda() {
                    if (!this.paisData || !this.paisData.currencies) return 'N/A';
                    return Object.values(this.paisData.currencies)
                        .map(c => `${c.name} (${c.symbol})`)
                        .join(', ');
                },

                obtenerIdiomas() {
                    if (!this.paisData || !this.paisData.languages) return 'N/A';
                    return Object.values(this.paisData.languages).join(', ');
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
