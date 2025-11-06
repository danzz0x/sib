<?php

namespace App\Livewire\Secciones;

use App\Enums\TipoMostrarSeccion;
use App\Models\Colegio;
use App\Models\Seccion;
use App\Traits\WithAlerts;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

class Crud extends Component
{
    use WithAlerts;

    public Colegio $colegio;

    public $isEditing = false;

    public $seccionId;

    public $titulo = '';

    public $tipo_mostrar = null;

    public $orden = 0;

    public $action;

    protected function rules()
    {
        $rules = [
            'titulo' => 'nullable|string|max:150',
            'tipo_mostrar' => 'required|in:'.implode(',', array_column(TipoMostrarSeccion::cases(), 'value')),
        ];

        return $rules;
    }

    protected $messages = [
        'titulo.max' => 'El título no puede exceder 150 caracteres',
        'tipo_mostrar.required' => 'Selecciona un tipo de visualización',
        'tipo_mostrar.in' => 'El tipo seleccionado no es válido',
    ];

    public function render()
    {
        return view('livewire.secciones.crud');
    }

    #[On('edit-seccion')]
    public function edit($id)
    {
        $seccion = Seccion::findOrFail($id);
        $this->seccionId = $seccion->id;
        $this->titulo = $seccion->titulo;
        $this->tipo_mostrar = $seccion->tipo_mostrar->value;
        $this->orden = $seccion->orden;
        $this->isEditing = true;
    }

    public function store()
    {

        $this->validate();

        if ($this->isEditing) {
            Seccion::where('id', $this->seccionId)
                ->update([
                    'titulo' => $this->titulo ?: null,
                    'tipo_mostrar' => $this->tipo_mostrar,
                ]);
            $this->toastUpdated('Sección');
        } else {
            $maxOrden = Seccion::where('colegio_id', $this->colegio->id)->max('orden') ?? 0;

            Seccion::create([
                'colegio_id' => $this->colegio->id,
                'titulo' => $this->titulo ?: null,
                'tipo_mostrar' => $this->tipo_mostrar,
                'orden' => $maxOrden + 1,
            ]);
            $this->toastCreated('Sección');
        }

        $this->clearColegioCache();
        $this->resetForm();
        $this->dispatch('seccion-updated');
        $this->dispatch('close-modal');

    }

    #[On('delete-seccion')]
    public function delete($id)
    {
        try {
            $seccion = Seccion::findOrFail($id);
            $orden = $seccion->orden;

            $publicaciones = $seccion->publicaciones;
            foreach ($publicaciones as $publicacion) {
                if ($publicacion->imagen && Storage::disk('public')->exists($publicacion->imagen)) {
                    Storage::disk('public')->delete($publicacion->imagen);
                }
            }
            $seccion->delete();

            Seccion::where('colegio_id', $this->colegio->id)
                ->where('orden', '>', $orden)
                ->decrement('orden');

            $this->clearColegioCache();
            $this->toastDeleted('Sección');
            $this->dispatch('seccion-updated');

        } catch (\Exception $e) {
            $this->toastError('Error al eliminar la sección');
        }
    }

    #[On('move-seccion-up')]
    public function moveUp($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $seccion = Seccion::lockForUpdate()->findOrFail($id);

                $seccionAnterior = Seccion::where('colegio_id', $this->colegio->id)
                    ->where('orden', '<', $seccion->orden)
                    ->orderBy('orden', 'desc')
                    ->lockForUpdate()
                    ->first();

                if (! $seccionAnterior) {
                    return;
                }

                $ordenActual = $seccion->orden;

                $seccion->update(['orden' => $seccionAnterior->orden]);
                $seccionAnterior->update(['orden' => $ordenActual]);
            });

            $this->clearColegioCache();
            $this->dispatch('seccion-updated');
            $this->toastSuccess('Sección movida hacia arriba');
        } catch (\Exception $e) {
            $this->toastError('Error al mover la sección');
        }
    }

    #[On('move-seccion-down')]
    public function moveDown($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $seccion = Seccion::lockForUpdate()->findOrFail($id);

                $seccionSiguiente = Seccion::where('colegio_id', $this->colegio->id)
                    ->where('orden', '>', $seccion->orden)
                    ->orderBy('orden', 'asc') // el más próximo hacia abajo
                    ->lockForUpdate()
                    ->first();

                if (! $seccionSiguiente) {
                    return;
                }

                $ordenActual = $seccion->orden;

                $seccion->update(['orden' => $seccionSiguiente->orden]);
                $seccionSiguiente->update(['orden' => $ordenActual]);
            });

            $this->clearColegioCache();
            $this->dispatch('seccion-updated');
            $this->toastSuccess('Sección movida hacia abajo');

        } catch (\Exception $e) {
            $this->toastError('Error al mover la sección');
        }
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['titulo', 'tipo_mostrar', 'orden', 'seccionId', 'isEditing']);
        $this->resetErrorBag();
    }

    private function clearColegioCache()
    {
        Cache::forget("colegio_optimized.{$this->colegio->slug}.guest");
        Cache::forget("colegio_optimized.{$this->colegio->slug}.auth");
        Cache::forget("colegio.{$this->colegio->slug}.guest");
        if (request()->user()) {
            Cache::forget("colegio.{$this->colegio->slug}.".request()->user()->id);
        }
    }
}
