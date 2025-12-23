<?php

namespace App\Notifications;

use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NuevoPagoPendiente extends Notification
{
    use Queueable;

    public $pago;

    public function __construct(Pago $pago)
    {
        $this->pago = $pago;
    }

    public function via($notifiable)
    {
        // database: Para guardar en tabla 'notifications'
        // broadcast: Para enviar por WebSocket (tiempo real)
        return ['database', 'broadcast'];
    }

    // Lo que se guarda en la BD
    public function toArray($notifiable)
    {
        return [
            'pago_id' => $this->pago->id,
            'socio_nombre' => $this->pago->socio->nombre,
            'monto' => $this->pago->monto_pagado,
            'mensaje' => 'Nuevo pago registrado por '.$this->pago->socio->nombre,
            'fecha' => now(),
        ];
    }

    // Lo que se envía al WebSocket (JS)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'pago_id' => $this->pago->id,
            'mensaje' => 'Nuevo pago de '.$this->pago->monto_pagado.' Bs.',
        ]);
    }
}
