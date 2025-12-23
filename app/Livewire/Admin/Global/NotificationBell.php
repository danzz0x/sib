<?php

namespace App\Livewire\Admin\Global;

use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public $count = 0;

    // 1. Escuchar el mismo canal que ValidarPagos
    public function getListeners()
    {
        return [
            // Cuando llegue una notificación, actualizamos el contador
            'echo-private:App.Models.User.'.Auth::id().',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'updateCount',
            // Opcional: Escuchar cuando se apruebe un pago en otra pestaña para bajar el contador
            'pago-procesado' => 'updateCount',
        ];
    }

    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $user = Auth::user();

        $query = Pago::where('estado', 'pendiente');

        // Misma lógica de filtro que tienes en ValidarPagos
        if ($user->isUsuarioColegio()) {
            $colegiosIds = $user->colegios->pluck('id');
            $query->whereHas('socio', function ($q) use ($colegiosIds) {
                $q->whereIn('id_colegio', $colegiosIds);
            });
        }

        $this->count = $query->count();
    }

    public function render()
    {
        return view('livewire.admin.global.notification-bell');
    }
}
