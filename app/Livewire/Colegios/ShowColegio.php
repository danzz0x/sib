<?php

namespace App\Livewire\Colegios;

use App\Models\Colegio;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ShowColegio extends Component
{
    use WithPagination;

    public $colegio;

    public function mount($slug)
    {
        $this->loadColegio($slug);

    }

    #[On('contacto-updated')]
    #[On('seccion-updated')]
    #[On('publicacion-updated')]
    public function refreshColegio()
    {
        $this->loadColegio($this->colegio->slug);
    }

    private function loadColegio($slug)
    {
        $this->colegio = Colegio::with([
            'contactos:id,colegio_id,tipo,valor',
            'secciones:id,colegio_id,titulo,tipo_mostrar,orden',
        ])
            ->select('id', 'nombre', 'slug', 'logo', 'descripcion', 'activo', 'created_at')
            ->where('slug', $slug)
            ->first();

        if (! $this->colegio) {
            abort(404);
        }
    }

    public function render()
    {
        if (! $this->colegio || $this->colegio->secciones->isEmpty()) {
            return view('livewire.colegios.show-colegio', [
                'secciones' => collect(),
            ]);
        }

        $seccionesConPublicacionesPaginadas = $this->colegio->secciones->map(function ($seccion) {

            request()->user() ? $query = $seccion->publicaciones() : $query = $seccion->publicaciones()->where('archivado', false);
            $seccion->publicaciones = $query->paginate(
                6,
                ['*'],
                'pageSeccion_'.$seccion->id
            );

            return $seccion;
        });

        return view('livewire.colegios.show-colegio', [
            'secciones' => $seccionesConPublicacionesPaginadas,
        ]);
    }

    private function getContactTarget($tipo): string
    {
        return in_array($tipo, ['facebook', 'instagram', 'twitter', 'youtube', 'otro']) ? '_blank' : '_self';
    }

    // Método para invalidar cache desde otros componentes
    public function clearCache()
    {
        $user = request()->user();
        $userType = $user ? 'auth' : 'guest';
        Cache::forget("colegio_optimized.{$this->colegio->slug}.{$userType}");

        // También limpiar cache del otro tipo de usuario
        $otherUserType = $userType === 'auth' ? 'guest' : 'auth';
        Cache::forget("colegio_optimized.{$this->colegio->slug}.{$otherUserType}");
    }
}
