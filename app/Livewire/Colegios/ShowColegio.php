<?php

namespace App\Livewire\Colegios;

use App\Models\Colegio;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
class ShowColegio extends Component
{
    use WithPagination;

    public $colegio;

    public $contactos;

    public $todasLasSecciones;

    public $aniosDisponibles = [];

    public $anioSeleccionado = null;

    protected $publicacionesPorSeccion = [];

    public function mount($slug)
    {
        $this->loadColegioBase($slug);
        $this->loadContactos();
        $this->loadTodasLasSecciones();
        $this->loadAniosDisponibles();

        if (is_null($this->anioSeleccionado) && ! empty($this->aniosDisponibles)) {
            $this->anioSeleccionado = max($this->aniosDisponibles);
        }
    }

    private function loadColegioBase($slug)
    {
        $this->colegio = Colegio::select(
            'id',
            'nombre',
            'slug',
            'logo',
            'descripcion',
            'activo',
            'created_at'
        )
            ->where('slug', $slug)
            ->firstOrFail(); // Lanza 404 automáticamente si no existe
    }

    private function loadContactos()
    {
        $this->contactos = $this->colegio
            ->contactos()
            ->select('id', 'colegio_id', 'tipo', 'valor')
            ->get();
    }

    private function loadTodasLasSecciones()
    {
        $this->todasLasSecciones = $this->colegio
            ->secciones()
            ->select('id', 'colegio_id', 'titulo', 'tipo_mostrar', 'orden', 'anio')
            ->orderBy('orden')
            ->get();
    }

    private function loadAniosDisponibles()
    {

        $this->aniosDisponibles = $this->todasLasSecciones
            ->where('tipo_mostrar', 'directorio')
            ->whereNotNull('anio')
            ->pluck('anio')
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

    }

    private function loadPublicacionesParaSeccion($seccion)
    {
        $cacheKey = "seccion_{$seccion->id}_page_".request()->get('pageSeccion_'.$seccion->id, 1);

        if (isset($this->publicacionesPorSeccion[$cacheKey])) {
            return $this->publicacionesPorSeccion[$cacheKey];
        }

        $query = $seccion->publicaciones();

        if (! request()->user()) {
            $query->where('archivado', false);
        }

        $publicaciones = $query->paginate(
            6,
            ['*'],
            'pageSeccion_'.$seccion->id
        );

        $this->publicacionesPorSeccion[$cacheKey] = $publicaciones;

        return $publicaciones;
    }

    private function getSeccionesVisibles(): Collection
    {
        return $this->todasLasSecciones->filter(function ($seccion) {
            if ($seccion->tipo_mostrar->value !== 'directorio') {
                return true;
            }

            return $seccion->anio === $this->anioSeleccionado;
        });
    }

    #[On('contacto-updated')]
    public function onContactoUpdated()
    {
        $this->loadContactos();
    }

    #[On('seccion-updated')]
    public function onSeccionUpdated()
    {
        $this->loadTodasLasSecciones();
        $this->loadAniosDisponibles();

        $this->publicacionesPorSeccion = [];
    }

    #[On('publicacion-updated')]
    public function onPublicacionUpdated($seccionId = null)
    {
        if ($seccionId) {
            $this->publicacionesPorSeccion = collect($this->publicacionesPorSeccion)
                ->reject(fn ($item, $key) => str_contains($key, "seccion_{$seccionId}_"))
                ->toArray();
        } else {
            $this->publicacionesPorSeccion = [];
        }
    }

    #[On('cambiarAnio')]
    public function cambiarAnio($anio)
    {
        $this->anioSeleccionado = $anio;
        $this->resetPage();

        $this->publicacionesPorSeccion = [];
    }

    #[Computed]
    public function canManageContent()
    {
        if (! auth()->check()) {
            return false;
        }

        auth()->user()->load('colegios');

        return Gate::allows('manage-post', $this->colegio);
    }

    public function render()
    {
        $seccionesVisibles = $this->getSeccionesVisibles();

        if ($seccionesVisibles->isEmpty()) {
            return view('livewire.colegios.show-colegio', [
                'secciones' => collect(),
            ]);
        }

        $seccionesConPublicaciones = $seccionesVisibles->map(function ($seccion) {
            // Clone para no modificar la colección original
            $seccionClone = clone $seccion;
            $seccionClone->publicaciones = $this->loadPublicacionesParaSeccion($seccion);

            return $seccionClone;
        });

        return view('livewire.colegios.show-colegio', [
            'secciones' => $seccionesConPublicaciones,
        ]);
    }

    public function clearCache()
    {
        $user = request()->user();
        $userType = $user ? 'auth' : 'guest';

        Cache::forget("colegio_optimized.{$this->colegio->slug}.{$userType}");
        Cache::forget("colegio_optimized.{$this->colegio->slug}.".($userType === 'auth' ? 'guest' : 'auth'));

        // Limpiar cache interno de publicaciones
        $this->publicacionesPorSeccion = [];
    }

    #[Computed]
    public function contactos()
    {
        return $this->contactos;
    }
}
