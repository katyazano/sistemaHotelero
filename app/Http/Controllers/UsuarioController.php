<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Mostrar el formulario de registro
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Guardar el nuevo usuario en la base de datos
     */
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:usuarios',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
            'pais' => 'required|string|max:100',
            'capital' => 'nullable|string|max:100',
            'moneda' => 'nullable|string|max:255',
            'idiomas' => 'nullable|string|max:255',
            'zona_horaria' => 'nullable|string|max:50',
        ], [
            'nombre.required' => 'El nombre es requerido',
            'email.required' => 'El email es requerido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'pais.required' => 'Debes seleccionar un país',
        ]);

        // Crear el usuario con los datos del formulario
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'pais' => $request->pais,
            'capital' => $request->capital,
            'moneda' => $request->moneda,
            'idiomas' => $request->idiomas,
            'zona_horaria' => $request->zona_horaria,
            'bandera_url' => $this->obtenerBanderaUrl($request->pais),
            'rol' => 'Huesped', // Por defecto, nuevo usuario es huésped
        ]);

        return redirect('/')->with('success', 'Usuario registrado exitosamente. Por favor inicia sesión.');
    }

    /**
     * Obtener solo la URL de la bandera del país
     */
    private function obtenerBanderaUrl($nombrePais)
    {
        try {
            $response = file_get_contents("https://restcountries.com/v3.1/name/{$nombrePais}?fields=flags");
            $datos = json_decode($response, true);

            if (!empty($datos)) {
                return $datos[0]['flags']['svg'] ?? null;
            }
        } catch (\Exception $e) {
            \Log::error('Error obteniendo bandera del país: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Obtener datos del país desde la API de restcountries
     */
    private function obtenerDatosDelPais($nombrePais)
    {
        try {
            $response = file_get_contents("https://restcountries.com/v3.1/name/{$nombrePais}");
            $datos = json_decode($response, true);

            if (!empty($datos)) {
                $pais = $datos[0];

                // Extraer moneda
                $moneda = 'N/A';
                if (isset($pais['currencies']) && is_array($pais['currencies'])) {
                    $monedas = array_map(function($c) {
                        return "{$c['name']} ({$c['symbol']})";
                    }, $pais['currencies']);
                    $moneda = implode(', ', $monedas);
                }

                // Extraer idiomas
                $idiomas = 'N/A';
                if (isset($pais['languages']) && is_array($pais['languages'])) {
                    $idiomas = implode(', ', array_values($pais['languages']));
                }

                return [
                    'capital' => $pais['capital'][0] ?? null,
                    'moneda' => $moneda,
                    'idiomas' => $idiomas,
                    'zona_horaria' => $pais['timezones'][0] ?? null,
                    'bandera_url' => $pais['flags']['svg'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error obteniendo datos del país: ' . $e->getMessage());
        }

        return [
            'capital' => null,
            'moneda' => null,
            'idiomas' => null,
            'zona_horaria' => null,
            'bandera_url' => null,
        ];
    }
}
