<?php

namespace App\Livewire\Publicaciones;

use App\Models\Colegio;
use App\Models\Publicacion;
use App\Traits\WithAlerts;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class Crud extends Component
{
    use WithAlerts, WithFileUploads;

    public Colegio $colegio;

    public $seccion_id;

    public $isEditing = false;

    public $publicacionId;

    public $titulo = '';

    public $descripcion = '';

    public $imagen;

    public $imagen_actual = '';

    public $url = '';

    public $prioridad = 0;

    public $archivado = false;

    public $fecha_inicio = '';

    public $fecha_fin = '';

    public $ubicacion = '';

    public $detalles = '';

    public $action;

    protected function rules()
    {
        $rules = [
            'titulo' => 'nullable|string|max:200|required_without:imagen',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048|required_without:titulo',
            'url' => 'nullable|url|max:255',
            'prioridad' => 'integer|min:0|max:999',
            'archivado' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'ubicacion' => 'nullable|string|max:255',
        ];

        return $rules;
    }

    protected $messages = [
        'titulo.required_without:imagen' => 'Debe ingresar un título si no selecciona una imagen.',
        'imagen.required_without:titulo' => 'Debe seleccionar una imagen si no ingresa un título.',
        'required_without' => 'Debe llenar al menos uno de estos campos',
        'titulo.max' => 'El título no puede exceder 200 caracteres',
        'imagen.image' => 'El archivo debe ser una imagen',
        'imagen.max' => 'La imagen no puede exceder 2MB',
        'url.url' => 'Debe ser una URL válida',
        'url.max' => 'La URL es demasiado larga',
        'prioridad.integer' => 'La prioridad debe ser un número',
        'prioridad.min' => 'La prioridad no puede ser negativa',
        'prioridad.max' => 'La prioridad no puede exceder 999',
        'fecha_fin.after_or_equal' => 'La fecha fin debe ser posterior o igual a la fecha inicio',
        'ubicacion.max' => 'La ubicación no puede exceder 255 caracteres',
    ];

    #[On('create-pub')]
    public function putSeccion($id)
    {
        $this->seccion_id = $id;
    }

    public function render()
    {
        return view('livewire.publicaciones.crud');
    }

    #[On('edit-publicacion')]
    public function edit($id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $this->publicacionId = $publicacion->id;
        $this->titulo = $publicacion->titulo;
        $this->descripcion = $publicacion->descripcion;
        $this->imagen_actual = $publicacion->imagen;
        $this->url = $publicacion->url;
        $this->prioridad = $publicacion->prioridad;
        $this->archivado = $publicacion->archivado;
        $this->fecha_inicio = $publicacion->fecha_inicio?->format('Y-m-d\TH:i');
        $this->fecha_fin = $publicacion->fecha_fin?->format('Y-m-d\TH:i');
        $this->ubicacion = $publicacion->ubicacion;
        $this->isEditing = true;
    }

    public function storeDetalles()
    {
        $this->validate([
            'detalles' => 'nullable|string|max:10000',
        ]);
        if ($this->publicacionId) {
            $publicacion = Publicacion::findOrFail($this->publicacionId);
            $publicacion->detalles = $this->detalles ?: null;
            $publicacion->save();
        }
        $this->dispatch('close-modal-detalle');
        $this->dispatch('publicacion-updated');
        $this->toastCreated('Detalles');
    }

    #[On('store-detalles')]
    public function detalle($id)
    {
        $publicacion = Publicacion::findOrFail($id);
        $this->publicacionId = $publicacion->id;
        $this->detalles = $publicacion->detalles;

    }

    public function store()
    {
        $this->validate();

        $data = [
            'titulo' => $this->titulo ?: null,
            'descripcion' => $this->descripcion ?: null,
            'url' => $this->url ?: null,
            'archivado' => $this->archivado,
            'fecha_inicio' => $this->fecha_inicio ? Carbon::parse($this->fecha_inicio) : null,
            'fecha_fin' => $this->fecha_fin ? Carbon::parse($this->fecha_fin) : null,
            'ubicacion' => $this->ubicacion ?: null,
        ];
        if ($this->imagen) {
            try {
                if ($this->isEditing && $this->imagen_actual) {
                    if (Storage::disk('public')->exists($this->imagen_actual)) {
                        Storage::disk('public')->delete($this->imagen_actual);
                    }
                }

                $filename = 'pub-'.Str::slug($this->titulo ?: 'imagen').'-'.time().'.webp';

                $processedImage = Image::read($this->imagen->getRealPath())
                    ->toWebp(100);

                Storage::disk('public')->put("publicaciones/{$filename}", $processedImage);
                $data['imagen'] = "publicaciones/{$filename}";

            } catch (\Exception $e) {
                $this->toastError('Error procesando la imagen');

                return;
            }
        }

        try {
            if ($this->isEditing) {
                Publicacion::where('id', $this->publicacionId)->update($data);
                $this->toastUpdated('Publicación');
            } else {

                $maxOrden = Publicacion::where('seccion_id', $this->seccion_id)->max('prioridad') ?? 0;

                $data['seccion_id'] = $this->seccion_id;
                $data['prioridad'] = $maxOrden + 1;
                Publicacion::create($data);
                $this->toastCreated('Publicación');
            }

            $this->clearColegioCache();
            $this->resetForm();
            $this->dispatch('publicacion-updated');
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            $this->toastError('Error al guardar la publicación');
        }
    }

    #[On('delete-publicacion')]
    public function delete($id)
    {
        try {
            $publicacion = Publicacion::select(['id', 'imagen'])->findOrFail($id);

            if ($publicacion->imagen && Storage::disk('public')->exists($publicacion->imagen)) {
                Storage::disk('public')->delete($publicacion->imagen);
            }

            $publicacion->delete();

            $this->clearColegioCache();
            $this->toastDeleted('Publicación');
            $this->dispatch('publicacion-updated');

        } catch (\Exception $e) {
            $this->toastError('Error al eliminar la publicación');
        }
    }

    #[On('toggle-archivado')]
    public function toggleArchivado($id)
    {
        try {
            $publicacion = Publicacion::findOrFail($id);
            $publicacion->update(['archivado' => ! $publicacion->archivado]);

            $this->clearColegioCache();
            $estado = $publicacion->archivado ? 'archivada' : 'restaurada';
            $this->toastSuccess("Publicación $estado correctamente");
            $this->dispatch('publicacion-updated');
        } catch (\Exception $e) {
            $this->toastError('Error al cambiar el estado de la publicación');
        }
    }

    #[On('move-publicacion-up')]
    public function moveUp($id)
    {
        try {
            $publicacion = Publicacion::findOrFail($id);

            // Buscar publicación con mayor prioridad
            $publicacionSuperior = Publicacion::where('seccion_id', $publicacion->seccion_id)
                ->where('prioridad', '>', $publicacion->prioridad)
                ->orderBy('prioridad', 'asc')
                ->first();

            if ($publicacionSuperior) {
                // Intercambiar prioridades
                $prioridadActual = $publicacion->prioridad;
                $publicacion->update(['prioridad' => $publicacionSuperior->prioridad]);
                $publicacionSuperior->update(['prioridad' => $prioridadActual]);

                $this->clearColegioCache();
                $this->dispatch('publicacion-updated');
                $this->toastSuccess('Publicación movida hacia arriba');
            }
        } catch (\Exception $e) {
            $this->toastError('Error al mover la publicación');
        }
    }

    #[On('move-publicacion-down')]
    public function moveDown($id)
    {
        try {
            $publicacion = Publicacion::findOrFail($id);

            // Buscar publicación con menor prioridad
            $publicacionInferior = Publicacion::where('seccion_id', $publicacion->seccion_id)
                ->where('prioridad', '<', $publicacion->prioridad)
                ->orderBy('prioridad', 'desc')
                ->first();

            if ($publicacionInferior) {
                // Intercambiar prioridades
                $prioridadActual = $publicacion->prioridad;
                $publicacion->update(['prioridad' => $publicacionInferior->prioridad]);
                $publicacionInferior->update(['prioridad' => $prioridadActual]);

                $this->clearColegioCache();
                $this->dispatch('publicacion-updated');
                $this->toastSuccess('Publicación movida hacia abajo');
            }
        } catch (\Exception $e) {
            $this->toastError('Error al mover la publicación');
        }
    }

    public function removeImage()
    {
        if ($this->imagen_actual) {
            if (Storage::disk('public')->exists($this->imagen_actual)) {
                Storage::disk('public')->delete($this->imagen_actual);
            }

            if ($this->isEditing) {
                Publicacion::where('id', $this->publicacionId)->update(['imagen' => null]);
            }

            $this->imagen_actual = '';
            $this->clearColegioCache();
            $this->toastSuccess('Imagen eliminada correctamente');
            $this->dispatch('publicacion-updated');
        }
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset([
            'titulo', 'descripcion', 'imagen', 'imagen_actual', 'url',
            'archivado', 'fecha_inicio', 'fecha_fin', 'ubicacion',
            'publicacionId', 'isEditing', 'seccion_id', 'detalles',
        ]);

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
