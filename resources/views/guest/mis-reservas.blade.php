@extends('layouts.app')

@section('title', 'Mis Reservas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold"><i class="bi bi-calendar2-heart"></i> Mis Reservas</h1>
</div>

@if($reservas->isEmpty())
    <div class="alert alert-info">No tienes reservas registradas aún.</div>
@else
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Folio</th>
                    <th>Habitación(es)</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Total</th>
                    <th>Estado Pago</th>
                    <th>Estado Reserva</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservas as $reserva)
                <tr>
                    <td class="fw-bold">{{ $reserva->folio }}</td>
                    <td>
                        @foreach($reserva->detalles as $d)
                            <span class="badge bg-secondary">#{{ $d->habitacion->numero ?? '?' }} {{ $d->habitacion->tipo ?? '' }}</span>
                        @endforeach
                    </td>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha_entrada)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($reserva->fecha_salida)->format('d/m/Y') }}</td>
                    <td class="fw-bold">${{ number_format($reserva->total, 2) }}</td>
                    <td>
                        @php $colorPago = match($reserva->estado_pago) { 'pagado' => 'success', 'cancelado' => 'danger', default => 'warning text-dark' }; @endphp
                        <span class="badge bg-{{ $colorPago }}">{{ ucfirst($reserva->estado_pago) }}</span>
                    </td>
                    <td>
                        @php
                            $colorEstado = match($reserva->estado_reserva) { 'confirmada' => 'success', 'check_in' => 'info', 'check_out' => 'secondary', 'cancelada' => 'danger', default => 'warning text-dark' };
                            $labelEstado = match($reserva->estado_reserva) { 'check_in' => 'Check-in', 'check_out' => 'Check-out', default => ucfirst($reserva->estado_reserva) };
                        @endphp
                        <span class="badge bg-{{ $colorEstado }}">{{ $labelEstado }}</span>
                    </td>
                    <td class="text-center">
                        @if($reserva->estado_reserva === 'confirmada' && $reserva->estado_pago === 'pendiente')
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('reservas.pagar.form', $reserva) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-credit-card"></i> Pagar
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#cancelModal"
                                    data-folio="{{ $reserva->folio }}"
                                    data-action="{{ route('reservas.cancelar', $reserva) }}">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </button>
                            </div>
                        @elseif($reserva->estado_pago === 'pagado')
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pagado</span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Cancel confirmation modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirmar Cancelación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas cancelar la reserva <strong id="modalFolio"></strong>?
                Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, mantener</button>
                <form id="cancelForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Sí, cancelar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const cancelModal = document.getElementById('cancelModal');
    cancelModal.addEventListener('show.bs.modal', function (e) {
        const btn = e.relatedTarget;
        document.getElementById('modalFolio').textContent = btn.dataset.folio;
        document.getElementById('cancelForm').action = btn.dataset.action;
    });
</script>
@endpush
