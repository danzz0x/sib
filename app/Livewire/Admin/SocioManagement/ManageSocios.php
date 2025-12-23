<?php

namespace App\Livewire\Admin\SocioManagement;

use App\Enums\EspecialidadEnum;
use App\Models\Colegio;
use App\Models\Socio;
use App\Models\TipoSocio;
use Barryvdh\DomPDF\Facade\Pdf; // <--- No olvides importar esto
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageSocios extends Component
{
    use WithPagination;

    // --- Filtros ---
    public $search = '';

    public $filtro_colegio = '';

    public $filtro_tipo = '';

    public $filtro_estado = ''; // Opcional, pero útil

    // --- Modal y Edición ---
    public $openModal = false;

    public $socioId = null;

    // --- Campos Formulario ---
    public $id_tipo_socio;

    public $id_colegio;

    public $nombre;

    public $cedula;

    public $rni;

    public $especialidad = null;

    public $email;

    public $telefono;

    public $fecha_registro;

    public $estado = 'Activo';

    // --- Resetear Paginación al filtrar ---
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroColegio()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    // --- CONSULTA CENTRALIZADA (DRY: Don't Repeat Yourself) ---
    private function construirConsulta()
    {
        return Socio::with(['colegio', 'tipoSocio'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('cedula', 'like', '%'.$this->search.'%')
                        ->orWhere('rni', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filtro_colegio, fn ($q) => $q->where('id_colegio', $this->filtro_colegio))
            ->when($this->filtro_tipo, fn ($q) => $q->where('id_tipo_socio', $this->filtro_tipo))
            ->when($this->filtro_estado, fn ($q) => $q->where('estado', $this->filtro_estado))
            ->orderBy('id', 'desc');
    }

    public function render()
    {
        // Usamos la consulta centralizada y paginamos
        $socios = $this->construirConsulta()->paginate(10);

        return view('livewire.admin.socio-management.manage-socios', [
            'socios' => $socios,
            'colegios' => Colegio::where('activo', true)->get(),
            'tipos' => TipoSocio::all(),
            'especialidades' => EspecialidadEnum::cases(),
        ]);
    }

    // --- EXPORTAR PDF ---
    public function exportarPdf()
    {
        // Obtenemos los mismos datos que se ven en pantalla (sin paginar)
        $socios = $this->construirConsulta()->get();

        $pdf = Pdf::loadView('pdf.reporte-socios', [
            'socios' => $socios,
            'fecha' => now()->format('d/m/Y H:i'),
            'total' => $socios->count(),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_socios_'.now()->format('Ymd').'.pdf');
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'filtro_colegio', 'filtro_tipo', 'filtro_estado']);
    }

    // --- Resto de métodos CRUD (Create, Edit, Store, Toggle, Reset) ---
    // (Se mantienen idénticos a tu código anterior, solo cópialos aquí abajo)

    protected function rules()
    {
        return [
            'id_tipo_socio' => 'required|exists:tipos_socio,id',
            'id_colegio' => 'required|exists:colegios,id',
            'nombre' => 'required|string|max:150',
            'cedula' => ['required', 'string', 'max:20', Rule::unique('socios')->ignore($this->socioId)],
            'rni' => ['nullable', 'string', 'max:20', Rule::unique('socios')->ignore($this->socioId)],
            'especialidad' => ['required', Rule::enum(EspecialidadEnum::class)],
            'email' => ['nullable', 'email', 'max:100', Rule::unique('socios')->ignore($this->socioId)],
            'telefono' => 'nullable|string|max:20',
            'fecha_registro' => 'required|date',
            'estado' => 'required|in:Activo,Inactivo',
        ];
    }

    public function create()
    {
        $this->resetFields();
        $this->openModal = true;
    }

    public function edit($id)
    {
        $this->resetFields();
        $socio = Socio::findOrFail($id);
        $this->socioId = $socio->id;
        $this->id_tipo_socio = $socio->id_tipo_socio;
        $this->id_colegio = $socio->id_colegio;
        $this->nombre = $socio->nombre;
        $this->cedula = $socio->cedula;
        $this->rni = $socio->rni;
        $this->especialidad = $socio->especialidad->value;
        $this->email = $socio->email;
        $this->telefono = $socio->telefono;
        $this->fecha_registro = $socio->fecha_registro;
        $this->estado = $socio->estado;
        $this->openModal = true;
    }

    public function store()
    {
        $this->validate();
        Socio::updateOrCreate(['id' => $this->socioId], [
            'id_tipo_socio' => $this->id_tipo_socio, 'id_colegio' => $this->id_colegio,
            'nombre' => $this->nombre, 'cedula' => $this->cedula, 'rni' => $this->rni,
            'especialidad' => $this->especialidad, 'email' => $this->email,
            'telefono' => $this->telefono, 'fecha_registro' => $this->fecha_registro,
            'estado' => $this->estado,
        ]);
        $this->openModal = false;
        $this->resetFields();
        session()->flash('success', $this->socioId ? 'Socio actualizado.' : 'Socio registrado.');
    }

    public function toggleStatus($id)
    {
        $socio = Socio::findOrFail($id);
        $socio->estado = ($socio->estado === 'Activo') ? 'Inactivo' : 'Activo';
        $socio->save();
    }

    public function resetFields()
    {
        $this->reset(['socioId', 'id_tipo_socio', 'id_colegio', 'nombre', 'cedula', 'rni', 'especialidad', 'email', 'telefono']);
        $this->fecha_registro = now()->format('Y-m-d');
        $this->estado = 'Activo';
        $this->resetErrorBag();
    }
}
