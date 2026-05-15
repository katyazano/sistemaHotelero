<?php

namespace App\Http\Controllers;

use App\Mail\ReservaConfirmada;
use App\Models\DetalleReserva;
use App\Models\Habitacion;
use App\Models\Pago;
use App\Models\Reserva;
use App\Models\Usuario;
use App\Services\DisponibilidadService;
use App\Support\FolioGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ReservaController extends Controller
{
    public function __construct(private DisponibilidadService $disponibilidad) {}

    // ─── ADMIN: listado ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Reserva::with('usuario', 'detalles.habitacion')
            ->orderByDesc('id_reserva');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('folio', 'like', "%{$q}%")
                    ->orWhereHas('usuario', fn($u) => $u->where('nombre', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('estado_reserva')) {
            $query->where('estado_reserva', $request->estado_reserva);
        }

        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        $reservas = $query->paginate(15)->withQueryString();
        return view('reservas.index', compact('reservas'));
    }

    // ─── GUEST: mis reservas ───────────────────────────────────────────────
    public function misReservas()
    {
        $user = Auth::user();
        $usuario = Usuario::where('email', $user->email)->first();

        $reservas = $usuario
            ? Reserva::where('id_usuario', $usuario->id_usuario)
                ->with('detalles.habitacion')
                ->latest()
                ->get()
            : collect();

        return view('guest.mis-reservas', compact('reservas'));
    }

    // ─── GUEST: formulario de pago ─────────────────────────────────────────
    public function showPago(Reserva $reserva)
    {
        $user = Auth::user();

        if (!$reserva->usuario || $reserva->usuario->email !== $user->email) {
            abort(403);
        }

        if ($reserva->estado_pago === 'pagado' || $reserva->estado_reserva === 'cancelada') {
            return redirect()->route('mis-reservas')
                ->withErrors(['error' => 'Esta reserva no puede procesarse.']);
        }

        $reserva->load('detalles.habitacion');
        return view('guest.pagar', compact('reserva'));
    }

    // ─── GUEST: simular pago ───────────────────────────────────────────────
    public function pagarSimulado(Request $request, Reserva $reserva)
    {
        $user = Auth::user();

        if (!$reserva->usuario || $reserva->usuario->email !== $user->email) {
            abort(403);
        }

        if ($reserva->estado_pago === 'pagado' || $reserva->estado_reserva === 'cancelada') {
            return back()->withErrors(['error' => 'Esta reserva no puede procesarse.']);
        }

        $request->validate([
            'nombre_titular' => 'required|string|max:100',
            'tipo_pago'      => 'required|in:efectivo,tarjeta_credito,tarjeta_debito,transferencia',
            'numero_tarjeta' => 'nullable|digits:16',
            'expiracion'     => ['nullable', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv'            => 'nullable|digits_between:3,4',
        ]);

        DB::transaction(function () use ($request, $reserva) {
            $reserva->update(['estado_pago' => 'pagado']);

            Pago::create([
                'id_reserva'  => $reserva->id_reserva,
                'monto'       => $reserva->total,
                'fecha_pago'  => now(),
                'metodo_pago' => $request->tipo_pago,
            ]);
        });

        return redirect()->route('mis-reservas')
            ->with('success', "Pago registrado exitosamente. Folio: {$reserva->folio}");
    }

    // ─── GUEST: cancelar reserva propia ────────────────────────────────────
    public function cancelar(Reserva $reserva)
    {
        $user = Auth::user();

        if (!$reserva->usuario || $reserva->usuario->email !== $user->email) {
            abort(403);
        }

        if (!in_array($reserva->estado_reserva, ['pendiente', 'confirmada'], true)
            || $reserva->estado_pago === 'pagado') {
            return back()->withErrors(['error' => 'Esta reserva ya no puede cancelarse.']);
        }

        $reserva->update(['estado_reserva' => 'cancelada']);
        return back()->with('success', 'Reserva cancelada correctamente.');
    }

    // ─── GUEST: formulario crear reserva ───────────────────────────────────
    public function guestCreate(Request $request, Habitacion $habitacion)
    {
        $user = Auth::user();
        $usuario = $this->resolverUsuarioGuest($user);

        return view('reservas.guest-create', [
            'habitacion'    => $habitacion,
            'usuario'       => $usuario,
            'fechaEntrada'  => $request->input('fecha_entrada'),
            'fechaSalida'   => $request->input('fecha_salida'),
        ]);
    }

    // ─── GUEST: guardar reserva (1+ habitaciones, transacción + lock) ──────
    public function guestStore(Request $request)
    {
        $request->validate([
            'fecha_entrada'              => 'required|date|after_or_equal:today',
            'fecha_salida'               => 'required|date|after:fecha_entrada',
            'habitaciones'               => 'required|array|min:1',
            'habitaciones.*'             => 'required|integer|distinct|exists:habitaciones,id_habitacion',
        ]);

        $user = Auth::user();
        $usuario = $this->resolverUsuarioGuest($user);
        $noches = $this->disponibilidad->calcularNoches($request->fecha_entrada, $request->fecha_salida);

        try {
            $reserva = DB::transaction(function () use ($request, $usuario, $noches) {
                $total = 0;
                $habitacionesData = [];

                foreach ($request->habitaciones as $idHab) {
                    $habitacion = Habitacion::lockForUpdate()->findOrFail($idHab);

                    $hayConflicto = $this->disponibilidad->tieneConflicto(
                        idHabitacion: $habitacion->id_habitacion,
                        fechaEntrada: $request->fecha_entrada,
                        fechaSalida:  $request->fecha_salida,
                        forUpdate:    true
                    );

                    if ($hayConflicto) {
                        throw new \RuntimeException(
                            "La habitación #{$habitacion->numero} ({$habitacion->tipo}) ya no está disponible para esas fechas."
                        );
                    }

                    $subtotal = $habitacion->precio * $noches;
                    $total += $subtotal;
                    $habitacionesData[] = [
                        'habitacion' => $habitacion,
                        'subtotal'   => $subtotal,
                    ];
                }

                $reserva = Reserva::create([
                    'folio'          => FolioGenerator::nuevo(),
                    'fecha_entrada'  => $request->fecha_entrada,
                    'fecha_salida'   => $request->fecha_salida,
                    'estado_pago'    => 'pendiente',
                    'estado_reserva' => 'confirmada',
                    'total'          => $total,
                    'id_usuario'     => $usuario->id_usuario,
                ]);

                foreach ($habitacionesData as $item) {
                    DetalleReserva::create([
                        'id_reserva'      => $reserva->id_reserva,
                        'id_habitacion'   => $item['habitacion']->id_habitacion,
                        'precio_unitario' => $item['habitacion']->precio,
                        'subtotal'        => $item['subtotal'],
                    ]);
                }

                return $reserva;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Error creando reserva guest', ['ex' => $e->getMessage()]);
            return back()->withErrors(['error' => 'No fue posible crear la reserva. Intenta de nuevo.'])->withInput();
        }

        try {
            Mail::to($user->email)->send(new ReservaConfirmada($reserva));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar email de confirmación: ' . $e->getMessage());
        }

        return redirect()
            ->route('mis-reservas')
            ->with('success', "Reserva creada correctamente. Folio: {$reserva->folio}");
    }

    // ─── ADMIN: formulario crear reserva ───────────────────────────────────
    public function create()
    {
        $usuarios = Usuario::orderBy('nombre')->get();
        $habitaciones = Habitacion::orderBy('numero')->get();
        return view('reservas.create', compact('usuarios', 'habitaciones'));
    }

    // ─── ADMIN: guardar reserva ────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_usuario'                       => 'required|exists:usuarios,id_usuario',
            'fecha_entrada'                    => 'required|date|after_or_equal:today',
            'fecha_salida'                     => 'required|date|after:fecha_entrada',
            'detalles'                         => 'required|array|min:1',
            'detalles.*.id_habitacion'         => 'required|integer|distinct|exists:habitaciones,id_habitacion',
            'imagen'                           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imagen_url'                       => 'nullable|url',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagenUrl = Storage::url($request->file('imagen')->store('reservas', 'public'));
        } elseif ($request->filled('imagen_url')) {
            $imagenUrl = $request->imagen_url;
        }

        $noches = $this->disponibilidad->calcularNoches($request->fecha_entrada, $request->fecha_salida);

        try {
            $reserva = DB::transaction(function () use ($request, $noches, $imagenUrl) {
                $total = 0;
                $detallesData = [];

                foreach ($request->detalles as $detalle) {
                    $habitacion = Habitacion::lockForUpdate()->findOrFail($detalle['id_habitacion']);

                    $hayConflicto = $this->disponibilidad->tieneConflicto(
                        idHabitacion: $habitacion->id_habitacion,
                        fechaEntrada: $request->fecha_entrada,
                        fechaSalida:  $request->fecha_salida,
                        forUpdate:    true
                    );

                    if ($hayConflicto) {
                        throw new \RuntimeException(
                            "La habitación #{$habitacion->numero} NO está disponible en las fechas seleccionadas."
                        );
                    }

                    $subtotal = $habitacion->precio * $noches;
                    $total += $subtotal;
                    $detallesData[] = [
                        'habitacion' => $habitacion,
                        'subtotal'   => $subtotal,
                    ];
                }

                $reserva = Reserva::create([
                    'folio'          => FolioGenerator::nuevo(),
                    'fecha_entrada'  => $request->fecha_entrada,
                    'fecha_salida'   => $request->fecha_salida,
                    'estado_pago'    => 'pendiente',
                    'estado_reserva' => 'confirmada',
                    'total'          => $total,
                    'id_usuario'     => $request->id_usuario,
                    'imagen_url'     => $imagenUrl,
                ]);

                foreach ($detallesData as $d) {
                    DetalleReserva::create([
                        'id_reserva'      => $reserva->id_reserva,
                        'id_habitacion'   => $d['habitacion']->id_habitacion,
                        'precio_unitario' => $d['habitacion']->precio,
                        'subtotal'        => $d['subtotal'],
                    ]);
                }

                return $reserva;
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Error creando reserva admin', ['ex' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al crear la reserva.'])->withInput();
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva creada correctamente.');
    }

    // ─── ADMIN: ver / editar / actualizar / eliminar ───────────────────────
    public function show(Reserva $reserva)
    {
        $reserva->load('usuario', 'detalles.habitacion', 'pagos', 'checkInBy', 'checkOutBy');
        return view('reservas.show', compact('reserva'));
    }

    public function edit(Reserva $reserva)
    {
        $usuarios = Usuario::orderBy('nombre')->get();
        $habitaciones = Habitacion::orderBy('numero')->get();
        $reserva->load('detalles');
        return view('reservas.edit', compact('reserva', 'usuarios', 'habitaciones'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        $request->validate([
            'id_usuario'                   => 'required|exists:usuarios,id_usuario',
            'fecha_entrada'                => 'required|date',
            'fecha_salida'                 => 'required|date|after:fecha_entrada',
            'estado_pago'                  => 'required|in:pendiente,pagado,cancelado',
            'estado_reserva'               => 'required|in:pendiente,confirmada,cancelada',
            'detalles'                     => 'required|array|min:1',
            'detalles.*.id_detalle'        => 'nullable|exists:detalle_reservas,id_detalle',
            'detalles.*.id_habitacion'     => 'required|exists:habitaciones,id_habitacion',
        ]);

        if ($request->hasFile('imagen')) {
            if ($reserva->imagen_url) {
                $oldPath = str_replace('/storage/', '', $reserva->imagen_url);
                Storage::disk('public')->delete($oldPath);
            }
            $imagenUrl = Storage::url($request->file('imagen')->store('reservas', 'public'));
        } elseif ($request->filled('imagen_url')) {
            $imagenUrl = $request->imagen_url;
        } else {
            $imagenUrl = $reserva->imagen_url;
        }

        $noches = $this->disponibilidad->calcularNoches($request->fecha_entrada, $request->fecha_salida);

        try {
            DB::transaction(function () use ($request, $reserva, $noches, $imagenUrl) {
                $reserva->update([
                    'fecha_entrada'  => $request->fecha_entrada,
                    'fecha_salida'   => $request->fecha_salida,
                    'estado_pago'    => $request->estado_pago,
                    'estado_reserva' => $request->estado_reserva,
                    'id_usuario'     => $request->id_usuario,
                    'imagen_url'     => $imagenUrl,
                ]);

                $detallesExistentes = $reserva->detalles->pluck('id_detalle')->toArray();
                $detallesEnviados   = collect($request->detalles)->pluck('id_detalle')->filter()->toArray();
                $detallesAEliminar  = array_diff($detallesExistentes, $detallesEnviados);
                if (!empty($detallesAEliminar)) {
                    DetalleReserva::destroy($detallesAEliminar);
                }

                $total = 0;
                foreach ($request->detalles as $detalle) {
                    $habitacion = Habitacion::lockForUpdate()->findOrFail($detalle['id_habitacion']);

                    $hayConflicto = $this->disponibilidad->tieneConflicto(
                        idHabitacion:        $habitacion->id_habitacion,
                        fechaEntrada:        $request->fecha_entrada,
                        fechaSalida:         $request->fecha_salida,
                        excluyendoReservaId: $reserva->id_reserva,
                        forUpdate:           true,
                    );

                    if ($hayConflicto) {
                        throw new \RuntimeException(
                            "La habitación #{$habitacion->numero} NO está disponible en las fechas seleccionadas."
                        );
                    }

                    $subtotal = $habitacion->precio * $noches;
                    $total += $subtotal;

                    if (!empty($detalle['id_detalle'])) {
                        DetalleReserva::where('id_detalle', $detalle['id_detalle'])->update([
                            'id_habitacion'   => $habitacion->id_habitacion,
                            'precio_unitario' => $habitacion->precio,
                            'subtotal'        => $subtotal,
                        ]);
                    } else {
                        DetalleReserva::create([
                            'id_reserva'      => $reserva->id_reserva,
                            'id_habitacion'   => $habitacion->id_habitacion,
                            'precio_unitario' => $habitacion->precio,
                            'subtotal'        => $subtotal,
                        ]);
                    }
                }

                $reserva->update(['total' => $total]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            Log::error('Error actualizando reserva', ['ex' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Error al actualizar la reserva.'])->withInput();
        }

        if ($request->estado_reserva === 'confirmada' && $reserva->wasChanged('estado_reserva')) {
            try {
                Mail::to($reserva->usuario->email)->send(new ReservaConfirmada($reserva));
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar email de confirmación: ' . $e->getMessage());
            }
        }

        return redirect()->route('reservas.index')->with('success', 'Reserva actualizada correctamente.');
    }

    public function destroy(Reserva $reserva)
    {
        if ($reserva->imagen_url) {
            $path = str_replace('/storage/', '', $reserva->imagen_url);
            Storage::disk('public')->delete($path);
        }
        $reserva->delete();
        return redirect()->route('reservas.index')->with('success', 'Reserva eliminada.');
    }

    /**
     * Crea o reutiliza el registro en la tabla 'usuarios' que está enlazada
     * a las reservas. Mantiene sincronizados nombre/email con la tabla 'users'.
     */
    private function resolverUsuarioGuest($user): Usuario
    {
        $usuario = Usuario::where('email', $user->email)->first();

        if (!$usuario) {
            $usuario = Usuario::create([
                'email'    => $user->email,
                'nombre'   => $user->name,
                'password' => $user->password,
                'rol'      => 'Huesped',
            ]);
        } elseif ($usuario->nombre !== $user->name) {
            $usuario->update(['nombre' => $user->name]);
        }

        return $usuario;
    }
}
