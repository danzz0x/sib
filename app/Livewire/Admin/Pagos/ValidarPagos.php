<?php

namespace App\Livewire\Admin\Pagos;

use App\Models\Pago;
use App\Notifications\EnviarComprobantePago; // La notificación al socio (del paso anterior)
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// Para escuchar eventos

class ValidarPagos extends Component
{
    // Propiedad para el modal de rechazo
    public $pagoSeleccionado = null;

    public $motivoRechazo = '';

    public $showRechazoModal = false;

    // Escuchar evento de Echo (Websockets)
    // Cuando llegue una notificación de broadcast, recargamos la lista
    public function getListeners()
    {
        // Escuchamos el canal privado del usuario logueado
        return [
            'echo-private:App.Models.User.'.Auth::id().',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'refreshLista',
        ];
    }

    public function refreshLista()
    {
        // Método vacío solo para que Livewire se re-renderice
        $this->dispatch('play-sound'); // Opcional: Sonido en JS
    }

    public function render()
    {
        $user = Auth::user();

        // Obtener solo los PENDIENTES
        $query = Pago::with(['socio.colegio', 'concepto', 'metodoPago'])
            ->where('estado', 'pendiente');

        // Filtrar por colegio si es usuario restringido
        if ($user->isUsuarioColegio()) {
            $colegiosIds = $user->colegios->pluck('id');
            $query->whereHas('socio', function ($q) use ($colegiosIds) {
                $q->whereIn('id_colegio', $colegiosIds);
            });
        }

        $pagosPendientes = $query->orderBy('created_at', 'asc')->get();

        return view('livewire.admin.pagos.validar-pagos', compact('pagosPendientes'));
    }

    public function aprobar($id)
    {
        $pago = Pago::find($id);

        // 1. Actualizar Pago
        $pago->update([
            'estado' => 'aprobado',
            'id_usuario' => Auth::id(),
        ]);

        // 2. Enviar Notificación al Socio (Email con PDF)
        if ($pago->socio->email) {
            $pago->socio->notify(new EnviarComprobantePago($pago));
        }

        // TODO: Aquí integrarías WhatsApp si lo tienes configurado

        session()->flash('success', 'Pago aprobado y notificado al socio.');
    }

    // Abrir modal de rechazo
    public function confirmarRechazo($id)
    {
        $this->pagoSeleccionado = Pago::find($id);
        $this->motivoRechazo = '';
        $this->showRechazoModal = true;
    }

    public function rechazar()
    {
        $this->validate(['motivoRechazo' => 'required|min:5']);

        $this->pagoSeleccionado->update([
            'estado' => 'rechazado',
            'id_usuario' => Auth::id(),
        ]);

        // Notificar rechazo
        if ($this->pagoSeleccionado->socio->email) {
            $this->pagoSeleccionado->socio->notify(new EnviarComprobantePago($this->pagoSeleccionado, $this->motivoRechazo));
        }

        $this->showRechazoModal = false;
        session()->flash('error', 'Pago rechazado correctamente.');
    }
}
