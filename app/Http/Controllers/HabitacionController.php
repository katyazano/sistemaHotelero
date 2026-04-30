<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HabitacionController extends Controller
{
    /**
     * Mostrar listado de habitaciones (Admin).
     */
    public function index()
    {
        $habitaciones = Habitacion::orderBy('numero')->get();
        return view('habitaciones.index', compact('habitaciones'));
    }

    /**
     * Mostrar formulario para crear nueva habitación.
     */
    public function create()
    {
        return view('habitaciones.create');
    }

    /**
     * Guardar nueva habitación.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero',
            'tipo' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imagen_url' => 'nullable|url'
        ]);

        $data = $request->only(['numero', 'tipo', 'precio', 'capacidad', 'estado']);

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('imagen_habitaciones', 'public');
            $data['imagen_url'] = Storage::url($path);
        } elseif ($request->filled('imagen_url')) {
            $data['imagen_url'] = $request->imagen_url;
        }

        Habitacion::create($data);

        return redirect()->route('habitaciones.index')->with('success', 'Habitación creada correctamente.');
    }

    /**
     * Mostrar formulario para editar habitación.
     */
    public function edit(Habitacion $habitacion)
    {
        return view('habitaciones.edit', compact('habitacion'));
    }

    /**
     * Actualizar habitación existente.
     */
    public function update(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero,' . $habitacion->id_habitacion . ',id_habitacion',
            'tipo' => 'required|string|max:50',
            'precio' => 'required|numeric|min:0',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupada,mantenimiento',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imagen_url' => 'nullable|url'
        ]);

        $data = $request->only(['numero', 'tipo', 'precio', 'capacidad', 'estado']);

        // Manejo de imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($habitacion->imagen_url) {
                $oldPath = str_replace('/storage/', '', $habitacion->imagen_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('imagen')->store('imagen_habitaciones', 'public');
            $data['imagen_url'] = Storage::url($path);
        } elseif ($request->filled('imagen_url')) {
            $data['imagen_url'] = $request->imagen_url;
        }

        $habitacion->update($data);

        return redirect()->route('habitaciones.index')->with('success', 'Habitación actualizada correctamente.');
    }

    /**
     * Eliminar habitación.
     */
    public function destroy(Habitacion $habitacion)
    {
        // Verificar si tiene reservas activas
        $tieneReservasActivas = $habitacion->detalles()
            ->whereHas('reserva', function ($query) {
                $query->whereNotIn('estado_reserva', ['cancelada']);
            })
            ->exists();

        if ($tieneReservasActivas) {
            return back()->withErrors(['error' => 'No se puede eliminar la habitación porque tiene reservas activas.']);
        }

        // Eliminar imagen si existe
        if ($habitacion->imagen_url) {
            $path = str_replace('/storage/', '', $habitacion->imagen_url);
            Storage::disk('public')->delete($path);
        }

        $habitacion->delete();

        return redirect()->route('habitaciones.index')->with('success', 'Habitación eliminada correctamente.');
    }
}
