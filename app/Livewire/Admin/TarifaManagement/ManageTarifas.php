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

    // UI
    public $search = '';

    public $openModal = false;

    public $tarifaId = null;

    // Campos
    public $id_concepto;

    public $id_tipo_socio;

    public $id_colegio; // Nullable

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

            // Validación de Unicidad Compuesta:
            // No permite repetir (Concepto + Tipo + Colegio)
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
        'id_concepto.unique' => 'Ya existe una tarifa configurada para esta combinación de Concepto, Socio y Colegio.',
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
        // Convertir string vacía a null para id_colegio (importante para la DB)
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
