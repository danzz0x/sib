<?php

namespace App\Livewire\Admin\Cuentas;

use App\Models\Colegio;
use App\Models\CuentaBancaria;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageCuentas extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $search = '';

    public $openModal = false;

    public $cuentaId = null;

    // Campos
    public $id_colegio = '';

    public $banco;

    public $nro_cuenta;

    public $titular;

    public $qr_imagen;

    public $qr_path_actual;

    public $activo = true;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'id_colegio' => 'nullable|exists:colegios,id',
            'banco' => 'required|string|max:50',
            'nro_cuenta' => 'required|string|max:50',
            'titular' => 'required|string|max:100',
            'qr_imagen' => 'nullable|image|max:2048',
            'activo' => 'boolean',
        ];
    }

    protected $messages = [
        'id_colegio.exists' => 'El colegio seleccionado no es válido.',

        'banco.required' => 'El nombre del banco es obligatorio (Ej: Banco Unión).',
        'banco.max' => 'El nombre del banco es demasiado largo (máximo 50 caracteres).',

        'nro_cuenta.required' => 'El número de cuenta es obligatorio.',
        'nro_cuenta.max' => 'El número de cuenta es demasiado largo.',

        'titular.required' => 'Debe ingresar el nombre del titular de la cuenta.',
        'titular.max' => 'El nombre del titular excede el límite de caracteres.',

        'qr_imagen.image' => 'El archivo subido debe ser una imagen (JPG, PNG).',
        'qr_imagen.max' => 'La imagen del QR es muy pesada (Máximo 2MB).',

        'activo.boolean' => 'El valor del estado activo/inactivo no es válido.',
    ];

    public function render()
    {
        $cuentas = CuentaBancaria::with('colegio')
            ->where(function ($q) {
                $q->where('banco', 'like', '%'.$this->search.'%')
                    ->orWhere('titular', 'like', '%'.$this->search.'%')
                    ->orWhere('nro_cuenta', 'like', '%'.$this->search.'%');
            })
            ->orderBy('id_colegio', 'asc')
            ->orderBy('activo', 'desc')
            ->paginate(10);

        return view('livewire.admin.cuentas.manage-cuentas', [
            'cuentas' => $cuentas,
            'colegios' => Colegio::where('activo', true)->orderBy('nombre')->get(),
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
        $cuenta = CuentaBancaria::findOrFail($id);

        $this->cuentaId = $cuenta->id;
        $this->id_colegio = $cuenta->id_colegio;
        $this->banco = $cuenta->banco;
        $this->nro_cuenta = $cuenta->nro_cuenta;
        $this->titular = $cuenta->titular;
        $this->qr_path_actual = $cuenta->qr_path;
        $this->activo = $cuenta->activo;

        $this->openModal = true;
    }

    public function store()
    {
        $this->validate();

        $qrPath = $this->qr_path_actual;

        if ($this->qr_imagen) {

            if ($this->qr_path_actual) {
                Storage::disk('public')->delete($this->qr_path_actual);
            }
            $qrPath = $this->qr_imagen->store('qrs_bancos', 'public');
        }

        $colegioId = $this->id_colegio === '' ? null : $this->id_colegio;

        CuentaBancaria::updateOrCreate(
            ['id' => $this->cuentaId],
            [
                'id_colegio' => $colegioId,
                'banco' => $this->banco,
                'nro_cuenta' => $this->nro_cuenta,
                'titular' => $this->titular,
                'qr_path' => $qrPath,
                'activo' => $this->activo,
            ]
        );

        $this->openModal = false;
        $this->resetFields();
        session()->flash('success', $this->cuentaId ? 'Cuenta actualizada correctamente.' : 'Cuenta registrada correctamente.');
    }

    public function toggleStatus($id)
    {
        $cuenta = CuentaBancaria::findOrFail($id);
        $cuenta->activo = ! $cuenta->activo;
        $cuenta->save();
    }

    public function resetFields()
    {
        $this->reset(['cuentaId', 'id_colegio', 'banco', 'nro_cuenta', 'titular', 'qr_imagen', 'qr_path_actual']);
        $this->activo = true;
        $this->resetErrorBag();
    }
}
