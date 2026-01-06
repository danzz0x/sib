<?php

namespace App\Livewire\Admin\Pagos;

use App\Models\Pago;
use App\Notifications\EnviarComprobantePago;
use App\Traits\WithAlerts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ValidarPagos extends Component
{
    use WithAlerts;

    public $pagoSeleccionado = null;

    public $motivoRechazo = '';

    public $showRechazoModal = false;

    public function getListeners()
    {
        return [
            'echo-private:App.Models.User.'.Auth::id().',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'refreshLista',
        ];
    }

    public function refreshLista()
    {
        $this->dispatch('play-sound');
    }

    public function render()
    {
        $user = Auth::user();

        $query = Pago::with(['socio.colegio', 'concepto', 'metodoPago'])
            ->where('estado', 'pendiente');

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

        $pago->update([
            'estado' => 'aprobado',
            'id_usuario' => Auth::id(),
        ]);

        if ($pago->socio->email) {
            $pago->socio->notify(new EnviarComprobantePago($pago));
        }

        // TODO: Aquí se integraria WhatsApp si lo tienes configurado

        $this->alertSuccess('Pago aprobado correctamente.', 'success');
    }

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

        if ($this->pagoSeleccionado->socio->email) {
            $this->pagoSeleccionado->socio->notify(new EnviarComprobantePago($this->pagoSeleccionado, $this->motivoRechazo));
        }

        $this->showRechazoModal = false;
        $this->toast('Pago rechazado correctamente.', 'success');
    }
}
