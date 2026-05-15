@php
$colorEstado = match($estado_reserva) {
    'confirmada' => 'success',
    'check_in'   => 'info',
    'check_out'  => 'secondary',
    'cancelada'  => 'danger',
    default      => 'warning text-dark',
};
$labelEstado = match($estado_reserva) {
    'check_in'  => 'Check-in',
    'check_out' => 'Check-out',
    'pendiente' => 'Pendiente',
    'confirmada'=> 'Confirmada',
    'cancelada' => 'Cancelada',
    default     => ucfirst($estado_reserva),
};
$colorPago = match($estado_pago) {
    'pagado'    => 'success',
    'cancelado' => 'danger',
    default     => 'warning text-dark',
};
@endphp
