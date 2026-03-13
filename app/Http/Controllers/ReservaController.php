<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Support\Facades\Storage;
use App\Models\Usuario;
use App\Models\Habitacion;
use App\Models\DetalleReserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    /**
     * Mostrar listado de reservas.
     */
    public function index()
    {
        $reservas = Reserva::with('usuario', 'detalles.habitacion')->get();
        return view('reservas.index', compact('reservas'));
    }

    /**
     * Mostrar formulario para crear nueva reserva.
     */
    public function create()
    {
        $usuarios = Usuario::all(); // Lista de clientes
        $habitaciones = Habitacion::where('estado', 'disponible')->get(); // Solo habitaciones disponibles
        return view('reservas.create', compact('usuarios', 'habitaciones'));
    }

    /**
     * Guardar una nueva reserva en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'fecha_entrada' => 'required|date|after_or_equal:today',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_habitacion' => 'required|exists:habitaciones,id_habitacion',
            'detalles.*.cantidad_personas' => 'required|integer|min:1',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // si se sube archivo
            'imagen_url' => 'nullable|url'
        ]);

        $data = $request->only(['id_usuario', 'fecha_entrada', 'fecha_salida', 'estado_pago', 'estado_reserva']); // ajusta según tus campos

    // Manejo de imagen
    if ($request->hasFile('imagen')) {
        $path = $request->file('imagen')->store('reservas', 'public');
        $data['imagen_url'] = Storage::url($path);
    } elseif ($request->filled('imagen_url')) {
        $data['imagen_url'] = $request->imagen_url;
    }

        // Calcular número de noches
        $noches = (strtotime($request->fecha_salida) - strtotime($request->fecha_entrada)) / 86400;

        // Generar folio único (ejemplo: RES-20250312-ABCD)
        $folio = 'RES-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        DB::beginTransaction();
        try {
            // Crear la reserva (inicialmente total = 0, se actualizará después)
            $reserva = Reserva::create([
                'folio' => $folio,
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida' => $request->fecha_salida,
                'estado_pago' => 'pendiente',
                'estado_reserva' => 'confirmada', // Puede ser 'pendiente' si prefieres
                'total' => 0,
                'id_usuario' => $request->id_usuario,
                'imagen_url' => $data['imagen_url'] ?? null,
            ]);

            $total = 0;

            // Procesar cada detalle (habitación seleccionada)
            foreach ($request->detalles as $detalle) {
                $habitacion = Habitacion::findOrFail($detalle['id_habitacion']);

                // Validar capacidad
                if ($detalle['cantidad_personas'] > $habitacion->capacidad) {
                    throw new \Exception("La habitación {$habitacion->numero} excede su capacidad máxima de {$habitacion->capacidad} personas.");
                }

                // Validar disponibilidad en fechas (evitar doble reserva)
                $conflicto = DetalleReserva::where('id_habitacion', $habitacion->id_habitacion)
                    ->whereHas('reserva', function ($query) use ($request) {
                        $query->where(function ($q) use ($request) {
                            $q->whereBetween('fecha_entrada', [$request->fecha_entrada, $request->fecha_salida])
                              ->orWhereBetween('fecha_salida', [$request->fecha_entrada, $request->fecha_salida])
                              ->orWhere(function ($q2) use ($request) {
                                  $q2->where('fecha_entrada', '<=', $request->fecha_entrada)
                                     ->where('fecha_salida', '>=', $request->fecha_salida);
                              });
                        })
                        ->whereNotIn('estado_reserva', ['cancelada']); // No considerar reservas canceladas
                    })
                    ->exists();

                if ($conflicto) {
                    throw new \Exception("La habitación {$habitacion->numero} no está disponible en las fechas seleccionadas.");
                }

                $subtotal = $habitacion->precio * $noches;
                $total += $subtotal;

                DetalleReserva::create([
                    'id_reserva' => $reserva->id_reserva,
                    'id_habitacion' => $habitacion->id_habitacion,
                    'cantidad_personas' => $detalle['cantidad_personas'],
                    'precio_unitario' => $habitacion->precio,
                    'subtotal' => $subtotal,
                ]);
            }

            // Actualizar total de la reserva
            $reserva->update(['total' => $total]);

            DB::commit();
            return redirect()->route('reservas.index')->with('success', 'Reserva creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la reserva: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar los detalles de una reserva específica.
     */
    public function show(Reserva $reserva)
    {
        $reserva->load('usuario', 'detalles.habitacion', 'pagos'); 
        return view('reservas.show', compact('reserva'));
    }

    /**
     * Mostrar formulario para editar una reserva.
     */
    public function edit(Reserva $reserva)
    {
        $usuarios = Usuario::all();
        $habitaciones = Habitacion::all(); // Podrías filtrar disponibles excepto las ya reservadas
        return view('reservas.edit', compact('reserva', 'usuarios', 'habitaciones'));
    }

    /**
     * Actualizar una reserva existente.
     */
    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'fecha_entrada' => 'required|date',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'estado_pago' => 'required|in:pendiente,pagado,cancelado',
            'estado_reserva' => 'required|in:confirmada,cancelada',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_detalle' => 'nullable|exists:detalle_reservas,id_detalle',
            'detalles.*.id_habitacion' => 'required|exists:habitaciones,id_habitacion',
            'detalles.*.cantidad_personas' => 'required|integer|min:1',
        ]);

        // Validación similar a store

        $data = $request->only(['id_usuario', 'fecha_entrada', 'fecha_salida', 'estado_pago', 'estado_reserva']);

        if ($request->hasFile('imagen')) {
        // Eliminar imagen anterior si existe
        if ($reserva->imagen_url) {
            $oldPath = str_replace('/storage/', '', $reserva->imagen_url);
            Storage::disk('public')->delete($oldPath);
        }
            $path = $request->file('imagen')->store('reservas', 'public');
            $data['imagen_url'] = Storage::url($path);
        } elseif ($request->filled('imagen_url')) {
            $data['imagen_url'] = $request->imagen_url;
        }   
        

        $noches = (strtotime($request->fecha_salida) - strtotime($request->fecha_entrada)) / 86400;

        DB::beginTransaction();
        try {
            // Actualizar datos principales de la reserva
            $reserva->update([
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida' => $request->fecha_salida,
                'estado_pago' => $request->estado_pago,
                'estado_reserva' => $request->estado_reserva,
                'id_usuario' => $request->id_usuario,
            ]);

            // Obtener IDs de detalles actuales
            $detallesExistentes = $reserva->detalles->pluck('id_detalle')->toArray();
            $detallesEnviados = collect($request->detalles)->pluck('id_detalle')->filter()->toArray();

            // Eliminar detalles que ya no están
            $detallesAEliminar = array_diff($detallesExistentes, $detallesEnviados);
            DetalleReserva::destroy($detallesAEliminar);

            $total = 0;

            // Procesar cada detalle (nuevos o existentes)
            foreach ($request->detalles as $detalle) {
                $habitacion = Habitacion::findOrFail($detalle['id_habitacion']);

                if ($detalle['cantidad_personas'] > $habitacion->capacidad) {
                    throw new \Exception("La habitación {$habitacion->numero} excede su capacidad.");
                }

                // Validar disponibilidad excepto para la misma reserva
                $conflicto = DetalleReserva::where('id_habitacion', $habitacion->id_habitacion)
                    ->where('id_detalle', '!=', $detalle['id_detalle'] ?? null) // Excluir el mismo detalle si es edición
                    ->whereHas('reserva', function ($query) use ($request, $reserva) {
                        $query->where('id_reserva', '!=', $reserva->id_reserva) // Excluir la reserva actual
                              ->where(function ($q) use ($request) {
                                  $q->whereBetween('fecha_entrada', [$request->fecha_entrada, $request->fecha_salida])
                                    ->orWhereBetween('fecha_salida', [$request->fecha_entrada, $request->fecha_salida])
                                    ->orWhere(function ($q2) use ($request) {
                                        $q2->where('fecha_entrada', '<=', $request->fecha_entrada)
                                           ->where('fecha_salida', '>=', $request->fecha_salida);
                                    });
                              })
                              ->whereNotIn('estado_reserva', ['cancelada']);
                    })
                    ->exists();

                if ($conflicto) {
                    throw new \Exception("La habitación {$habitacion->numero} no está disponible en las fechas seleccionadas.");
                }

                $subtotal = $habitacion->precio * $noches;
                $total += $subtotal;

                if (isset($detalle['id_detalle'])) {
                    // Actualizar detalle existente
                    $detalleModel = DetalleReserva::find($detalle['id_detalle']);
                    $detalleModel->update([
                        'id_habitacion' => $habitacion->id_habitacion,
                        'cantidad_personas' => $detalle['cantidad_personas'],
                        'precio_unitario' => $habitacion->precio,
                        'subtotal' => $subtotal,
                    ]);
                } else {
                    // Crear nuevo detalle
                    DetalleReserva::create([
                        'id_reserva' => $reserva->id_reserva,
                        'id_habitacion' => $habitacion->id_habitacion,
                        'cantidad_personas' => $detalle['cantidad_personas'],
                        'precio_unitario' => $habitacion->precio,
                        'subtotal' => $subtotal,
                    ]);
                }
            }

            // Actualizar total de la reserva
            $reserva->update(['total' => $total]);

            DB::commit();
            return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la reserva: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar una reserva.
     */
    public function destroy(Reserva $reserva)
    {
        if ($reserva->imagen_url) {
        $path = str_replace('/storage/', '', $reserva->imagen_url);
        Storage::disk('public')->delete($path);
        }
        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada.');
    }
}