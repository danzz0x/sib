<?php

namespace App\Livewire\Admin\TarifaManagement;

use App\Models\Colegio;
use App\Models\Concepto;
use App\Models\Tarifa;
use App\Models\TipoSocio;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageTarifas extends Component
{
    use WithPagination;

    public $search = '';

    public $openModal = false;

    public $tarifaId = null;

    public $id_concepto;

    public $id_tipo_socio;

    public $id_colegio;

    public $monto;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'id_concepto' => 'required|exists:conceptos,id',
            'id_tipo_socio' => 'required|exists:tipos_socio,id',
            'id_colegio' => 'nullable|exists:colegios,id',
            'monto' => 'required|numeric|min:0',

            'id_concepto' => [
                'required',
                Rule::unique('tarifas')->where(function ($query) {
                    return $query->where('id_tipo_socio', $this->id_tipo_socio)
                        ->where('id_colegio', $this->id_colegio);
                })->ignore($this->tarifaId),
            ],
        ];
    }

    protected $messages = [
        'id_concepto.required' => 'Debe seleccionar un concepto de pago.',
        'id_concepto.exists' => 'El concepto seleccionado no es válido.',

        'id_concepto.unique' => 'Ya existe una tarifa configurada idéntica (mismo Concepto, Tipo de Socio y Colegio).',

        'id_tipo_socio.required' => 'Seleccione a qué tipo de socio aplica esta tarifa.',
        'id_tipo_socio.exists' => 'El tipo de socio seleccionado no es válido.',

        'id_colegio.exists' => 'El colegio seleccionado no es válido.',

        'monto.required' => 'Es obligatorio ingresar el precio o monto.',
        'monto.numeric' => 'El monto debe ser un valor numérico.',
        'monto.min' => 'El monto no puede ser negativo.',
    ];

    public function render()
    {
        $tarifas = Tarifa::with(['concepto', 'tipoSocio', 'colegio'])
            ->whereHas('concepto', function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('colegio', function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.tarifa-management.manage-tarifas', [
            'tarifas' => $tarifas,
            'conceptos' => Concepto::all(),
            'tipos' => TipoSocio::all(),
            'colegios' => Colegio::where('activo', true)->get(),
        ]);
    }

    public function create()
    {
        $this->resetFields();
        $this->openModal = true;
    }

    public function edit($id)
    {
        $this->resetFields();
        $tarifa = Tarifa::findOrFail($id);

        $this->tarifaId = $tarifa->id;
        $this->id_concepto = $tarifa->id_concepto;
        $this->id_tipo_socio = $tarifa->id_tipo_socio;
        $this->id_colegio = $tarifa->id_colegio;
        $this->monto = $tarifa->monto;

        $this->openModal = true;
    }

    public function store()
    {
        if ($this->id_colegio === '') {
            $this->id_colegio = null;
        }

        $this->validate();

        Tarifa::updateOrCreate(
            ['id' => $this->tarifaId],
            [
                'id_concepto' => $this->id_concepto,
                'id_tipo_socio' => $this->id_tipo_socio,
                'id_colegio' => $this->id_colegio,
                'monto' => $this->monto,
            ]
        );

        $this->openModal = false;
        $this->resetFields();
        session()->flash('success', $this->tarifaId ? 'Tarifa actualizada correctamente.' : 'Tarifa creada correctamente.');
    }

    public function delete($id)
    {
        $tarifa = Tarifa::findOrFail($id);
        $tarifa->delete();
        session()->flash('success', 'Tarifa eliminada correctamente.');
    }

    public function resetFields()
    {
        $this->reset(['tarifaId', 'id_concepto', 'id_tipo_socio', 'id_colegio', 'monto']);
        $this->resetErrorBag();
    }
}
