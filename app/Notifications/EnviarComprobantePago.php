<?php

namespace App\Notifications;

use App\Models\Pago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnviarComprobantePago extends Notification
{
    use Queueable;

    public $pago;

    public $motivoRechazo;

    public function __construct(Pago $pago, $motivoRechazo = null)
    {
        $this->pago = $pago;
        $this->motivoRechazo = $motivoRechazo;
    }

    public function via($notifiable)
    {
        return ['mail']; // Aquí agregaremos 'whatsapp' en el futuro
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage);

        // CASO 1: PAGO APROBADO ✅
        if ($this->pago->estado === 'aprobado') {

            // 1. Generar PDF en Memoria
            $pdf = Pdf::loadView('pdf.recibo', ['pago' => $this->pago]);

            return $mail
                ->subject('✅ Comprobante de Pago - SIB Potosí')
                ->greeting('Estimado(a) Colega: '.$this->pago->socio->nombre)
                ->line('Nos complace informarle que su pago ha sido verificado y aprobado exitosamente.')
                ->line('Adjunto encontrará su recibo oficial en formato PDF.')
                ->line('Detalle: '.$this->pago->concepto->nombre.' - '.$this->pago->monto_pagado.' Bs.')
                // 2. Adjuntar el PDF
                ->attachData($pdf->output(), 'Recibo-SIB-'.$this->pago->id.'.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->salutation('Atentamente, Tesorería SIB Potosí');
        }

        // CASO 2: PAGO RECHAZADO ❌
        else {
            return $mail
                ->subject('⚠️ Acción Requerida: Pago Observado')
                ->error() // Pone el botón en rojo
                ->greeting('Hola '.$this->pago->socio->nombre)
                ->line('Hemos revisado su intento de pago y no ha podido ser procesado.')
                ->line('**Motivo del rechazo:**')
                ->line($this->motivoRechazo ?? 'La imagen del comprobante no es legible o el monto no coincide.')
                ->action('Corregir Pago', route('dashboard')) // Asegúrate de tener esta ruta
                ->line('Por favor, intente realizar el pago nuevamente.');
        }
    }
}
