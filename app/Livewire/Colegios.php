<?php

namespace App\Livewire;

use App\Models\Colegio;
use App\Traits\WithAlerts;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Livewire\Component;
use Livewire\WithFileUploads;

class Colegios extends Component
{
    use WithAlerts, WithFileUploads;

    public $nombre;

    public $descripcion;

    public $nuevoLogo;

    public $logoPreview;

    public $colegioId;

    protected $rules = [
        'nombre' => [
            'required',
            'string',
            'max:255',
            'regex:/^.+\s\([A-Z0-9\-]+\)$/i',
        ],
        'descripcion' => 'nullable|string|max:500',
        'nuevoLogo' => 'nullable|image|max:2048',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.regex' => 'El nombre debe tener el formato: Colegio ... (ABREVIACION)',
        'nuevoLogo.image' => 'Debe ser una imagen válida.',
        'nuevoLogo.max' => 'La imagen no puede ser mayor a 2MB.',
    ];

    public function render()
    {
        $colegios = Colegio::select(['id', 'nombre', 'descripcion', 'logo', 'activo', 'slug', 'created_at'])
            ->latest()->get();

        return view('livewire.colegios', compact('colegios'));
    }

    public function store()
    {
        $this->validate();

        $data = [
            'nombre' => trim($this->nombre),
            'descripcion' => trim($this->descripcion),
        ];

        if ($this->nuevoLogo) {
            if ($this->colegioId) {
                $old = Colegio::select(['id', 'logo'])->find($this->colegioId);
                if ($old?->logo && Storage::disk('public')->exists($old->logo)) {
                    Storage::disk('public')->delete($old->logo);
                }
            }

            $filename = Str::slug($this->nombre).'-'.time().'.webp';

            $processedImage = Image::read($this->nuevoLogo->getRealPath())

                ->toWebp(100);

            Storage::disk('public')->put("colegios/{$filename}", $processedImage);
            $data['logo'] = "colegios/{$filename}";

        }

        try {
            if ($this->colegioId) {
                $colegio = Colegio::findOrFail($this->colegioId);
                $colegio->update($data);
                $this->toastUpdated('Colegio');
            } else {
                Colegio::create($data);
                $this->toastCreated('Colegio');
            }

            $this->resetFields();

        } catch (\Exception $e) {
            $this->toastError('Error al guardar el colegio');
        }

        $this->dispatch('close-modal');
    }

    public function edit($id)
    {
        $colegio = Colegio::select(['id', 'nombre', 'descripcion', 'logo'])
            ->findOrFail($id);

        $this->colegioId = $colegio->id;
        $this->nombre = $colegio->nombre;
        $this->descripcion = $colegio->descripcion;
        $this->logoPreview = $colegio->logo;
    }

    public function activar($id)
    {
        try {
            $colegio = Colegio::select(['id', 'activo'])->findOrFail($id);
            $colegio->activo = ! $colegio->activo;
            $colegio->save();

            if ($colegio->activo) {
                $this->toastActivated('Colegio');
            } else {
                $this->toastDeactivated('Colegio');
            }

        } catch (\Exception $e) {
            $this->toastError('Error al cambiar el estado');
        }
    }

    public function delete($id)
    {
        try {
            $colegio = Colegio::select(['id', 'logo'])->findOrFail($id);

            if ($colegio->logo && Storage::disk('public')->exists($colegio->logo)) {
                Storage::disk('public')->delete($colegio->logo);
            }

            $colegio->delete();
            $this->toastDeleted('Colegio');

        } catch (\Exception $e) {
            $this->toastError('No se pudo eliminar el colegio');
        }
    }

    public function resetFields()
    {
        $this->reset(['nombre', 'descripcion', 'nuevoLogo', 'logoPreview', 'colegioId']);
        $this->resetErrorBag();
    }
}
