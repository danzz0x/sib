<?php

namespace App\Livewire\Contactos;

use App\Enums\TipoContacto;
use App\Models\Colegio;
use App\Models\ColegioContacto;
use App\Traits\WithAlerts;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class Crud extends Component
{
    use WithAlerts;

    public Colegio $colegio;

    public $isEditing = false;

    public $contactoId;

    public $tipo = '';

    public $valor = '';

    public $action;

    protected function rules()
    {
        $rules = [
            'tipo' => 'required|in:'.implode(',', array_column(TipoContacto::cases(), 'value')),
            'valor' => 'required|string|max:255',
        ];

        switch ($this->tipo) {
            case 'email':
                $rules['valor'] .= '|email';
                break;
            case 'telefono':
                $rules['valor'] .= '|regex:/^[\+]?[0-9\-\(\)\s]+$/';
                break;
            case 'facebook':
            case 'instagram':
            case 'twitter':
            case 'youtube':
            case 'otro':
                $rules['valor'] .= '|url';
                break;
        }

        return $rules;
    }

    protected $messages = [
        'tipo.required' => 'Selecciona un tipo de contacto',
        'valor.required' => 'El valor es obligatorio',
        'valor.email' => 'Debe ser un email válido',
        'valor.url' => 'Debe ser una URL válida',
        'valor.regex' => 'Formato de teléfono no válido',
    ];

    public function render()
    {
        return view('livewire.contactos.crud');
    }

    #[On('edit-contacto')]
    public function edit($id)
    {
        $contacto = ColegioContacto::findOrFail($id);
        $this->contactoId = $contacto->id;
        $this->tipo = $contacto->tipo;
        $this->valor = $contacto->valor;
        $this->isEditing = true;
    }

    public function store()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                ColegioContacto::where('id', $this->contactoId)
                    ->update([
                        'tipo' => $this->tipo,
                        'valor' => $this->valor,
                    ]);
                $this->toastUpdated('Contacto');
            } else {
                ColegioContacto::create([
                    'colegio_id' => $this->colegio->id,
                    'tipo' => $this->tipo,
                    'valor' => $this->valor,
                ]);
                $this->toastCreated('Contacto');
            }

            $this->clearColegioCache();
            $this->resetForm();
            $this->dispatch('contacto-updated');
            $this->dispatch('close-modal');

        } catch (\Exception $e) {
            $this->toastError('Error al guardar el contacto');
        }
    }

    #[On('delete-contacto')]
    public function delete($id)
    {
        try {
            ColegioContacto::findOrFail($id)->delete();

            $this->clearColegioCache();
            $this->toastDeleted('Contacto');
            $this->dispatch('contacto-updated');

        } catch (\Exception $e) {
            $this->toastError('Error al eliminar el contacto');
        }
    }

    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset(['tipo', 'valor', 'contactoId', 'isEditing']);
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
