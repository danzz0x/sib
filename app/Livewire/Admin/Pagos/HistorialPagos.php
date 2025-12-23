<?php

namespace App\Livewire\Admin\Pagos;

use App\Models\Colegio;
use App\Models\Concepto;
use App\Models\Pago;
use App\Models\TipoSocio;
use Barryvdh\DomPDF\Facade\Pdf; // <--- Importante
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class HistorialPagos extends Component
{
    use WithPagination;

    // Filtros Básicos
    public $search = '';

    public $estado = '';

    public $fecha_desde = '';

    public $fecha_hasta = '';

    // Filtros Avanzados (NUEVOS)
    public $concepto_id = '';

    public $tipo_socio_id = '';

    public $colegio_id = '';

    // Resetear paginación al filtrar
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEstado()
    {
        $this->resetPage();
    }

    public function updatedFechaDesde()
    {
        $this->resetPage();
    }

    public function updatedFechaHasta()
    {
        $this->resetPage();
    }

    public function updatedConceptoId()
    {
        $this->resetPage();
    }

    public function updatedTipoSocioId()
    {
        $this->resetPage();
    }

    public function updatedColegioId()
    {
        $this->resetPage();
    }

    // --- LÓGICA CENTRALIZADA DE CONSULTA ---
    private function construirConsulta()
    {
        $user = Auth::user();

        // 1. Base + Eager Loading
        $query = Pago::with(['socio.colegio', 'socio.tipoSocio', 'concepto', 'metodoPago', 'usuario']);

        // 2. Restricción de Seguridad por Rol
        if ($user->isUsuarioColegio()) {
            // El cajero de colegio SOLO ve pagos de SUS colegios asignados
            $colegiosIds = $user->colegios->pluck('id');
            $query->whereHas('socio', function ($q) use ($colegiosIds) {
                $q->whereIn('id_colegio', $colegiosIds);
            });
        }

        // 3. Aplicar Filtros Dinámicos
        $query->when($this->search, function ($q) {
            $q->whereHas('socio', function ($s) {
                $s->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('rni', 'like', '%'.$this->search.'%')
                    ->orWhere('cedula', 'like', '%'.$this->search.'%');
            })
                ->orWhere('nro_transaccion', 'like', '%'.$this->search.'%');
        });

        $query->when($this->estado, fn ($q) => $q->where('estado', $this->estado));

        $query->when($this->fecha_desde, fn ($q) => $q->whereDate('fecha_pago', '>=', $this->fecha_desde));

        $query->when($this->fecha_hasta, fn ($q) => $q->whereDate('fecha_pago', '<=', $this->fecha_hasta));

        // Filtros Nuevos
        $query->when($this->concepto_id, fn ($q) => $q->where('id_concepto', $this->concepto_id));

        $query->when($this->tipo_socio_id, function ($q) {
            $q->whereHas('socio', fn ($s) => $s->where('id_tipo_socio', $this->tipo_socio_id));
        });

        $query->when($this->colegio_id, function ($q) {
            $q->whereHas('socio', fn ($s) => $s->where('id_colegio', $this->colegio_id));
        });

        return $query->orderBy('fecha_pago', 'desc');
    }

    public function render()
    {
        $user = Auth::user();

        // Obtener datos para los selects
        $conceptos = Concepto::all();
        $tiposSocio = TipoSocio::all();

        // Si es Admin Global ve todos, si no, solo sus colegios
        $colegios = $user->tieneAccesoGlobalPagos()
            ? Colegio::where('activo', true)->get()
            : $user->colegios;

        // Ejecutar consulta paginada
        $pagos = $this->construirConsulta()->paginate(15);

        return view('livewire.admin.pagos.historial-pagos', compact('pagos', 'conceptos', 'tiposSocio', 'colegios'));
    }

    // --- EXPORTAR PDF ---
    public function exportarPdf()
    {
        // Reutilizamos la misma consulta pero con get() en vez de paginate()
        $pagos = $this->construirConsulta()->get();

        // Generamos el PDF usando una vista Blade simple
        $pdf = Pdf::loadView('pdf.reporte-pagos', [
            'pagos' => $pagos,
            'fecha' => now()->format('d/m/Y H:i'),
            'usuario' => Auth::user()->name,
        ]);

        // Descarga directa (Stream)
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_pagos_'.now()->format('Ymd_Hi').'.pdf');
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'estado', 'fecha_desde', 'fecha_hasta', 'concepto_id', 'tipo_socio_id', 'colegio_id']);
    }
}
