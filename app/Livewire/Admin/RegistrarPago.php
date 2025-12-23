<?php

namespace App\Livewire\Admin;

use App\Models\Concepto;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Socio;
use App\Models\Tarifa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class RegistrarPago extends Component
{
    // --- Búsqueda ---
    public $search = '';

    public $resultados = [];

    public $socioSeleccionado = null;

    // --- Actualización de Datos de Contacto (NUEVO) ---
    public $nuevo_telefono;

    public $nuevo_email;

    public $pedir_telefono = false;

    public $pedir_email = false;

    // --- Formulario de Pago ---
    public $concepto_id;

    public $metodo_pago_id;

    public $nro_transaccion;

    public $observacion;

    // --- Finanzas y Fechas ---
    public $monto_total = 0;

    public $tarifa_unitaria = 0;

    // Periodos
    public $usar_periodos = false;

    public $fecha_inicio;

    public $fecha_fin;

    public $listaMeses = [];

    public function mount()
    {
        $hoy = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;

        $inicio = now()->subYear()->startOfMonth();
        for ($i = 0; $i < 36; $i++) {
            $fecha = $inicio->copy()->addMonths($i);
            $this->listaMeses[$fecha->format('Y-m-d')] = ucfirst($fecha->locale('es')->monthName).' '.$fecha->year;
        }
    }

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->resultados = [];

            return;
        }

        $this->resultados = Socio::with('tipoSocio', 'colegio')
            ->where('nombre', 'like', "%{$this->search}%")
            ->orWhere('rni', 'like', "%{$this->search}%")
            ->orWhere('cedula', 'like', "%{$this->search}%")
            ->limit(5)
            ->get();
    }

    public function seleccionarSocio($id)
    {
        $this->socioSeleccionado = Socio::with('tipoSocio', 'colegio')->find($id);
        $this->search = '';
        $this->resultados = [];
        $this->resetFormularioPago();

        // --- LÓGICA PARA DETECTAR DATOS FALTANTES ---
        // Verificamos si faltan datos. Si el campo es null o vacío, pedimos actualizar.
        $this->pedir_telefono = empty($this->socioSeleccionado->telefono);
        $this->pedir_email = empty($this->socioSeleccionado->email);

        // Limpiamos los inputs temporales
        $this->nuevo_telefono = '';
        $this->nuevo_email = '';
    }

    public function cancelarSeleccion()
    {
        $this->socioSeleccionado = null;
        $this->resetFormularioPago();
    }

    public function updatedConceptoId()
    {
        if (! $this->socioSeleccionado || ! $this->concepto_id) {
            return;
        }

        $concepto = Concepto::find($this->concepto_id);
        $this->usar_periodos = $concepto->es_periodico;

        $destinoColegioId = null;
        if (str_contains(strtolower($concepto->nombre), 'colegio')) {
            $destinoColegioId = $this->socioSeleccionado->id_colegio;
        }

        $tarifa = Tarifa::buscarPrecio(
            $this->concepto_id,
            $this->socioSeleccionado->id_tipo_socio,
            $destinoColegioId
        )->first();

        if ($tarifa) {
            $this->tarifa_unitaria = $tarifa->monto;
        } else {
            $this->tarifa_unitaria = 0;
            $this->addError('concepto_id', 'No existe una tarifa configurada.');
        }

        $this->calcularTotal();
    }

    public function updatedFechaInicio()
    {
        $this->validarFechas();
        $this->calcularTotal();
    }

    public function updatedFechaFin()
    {
        $this->validarFechas();
        $this->calcularTotal();
    }

    private function validarFechas()
    {
        if ($this->fecha_fin < $this->fecha_inicio) {
            $this->fecha_fin = $this->fecha_inicio;
        }
    }

    public function calcularTotal()
    {
        if ($this->usar_periodos) {
            $inicio = Carbon::parse($this->fecha_inicio);
            $fin = Carbon::parse($this->fecha_fin);
            $meses = $inicio->diffInMonths($fin) + 1;
            $this->monto_total = $this->tarifa_unitaria * $meses;
        } else {
            $this->monto_total = $this->tarifa_unitaria;
        }
    }

    public function guardar()
    {
        // 1. REGLAS DE VALIDACIÓN DINÁMICAS
        $rules = [
            'socioSeleccionado' => 'required',
            'concepto_id' => 'required|exists:conceptos,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'monto_total' => 'required|numeric|min:0.1',
        ];

        // Si falta teléfono, lo hacemos obligatorio ahora
        if ($this->pedir_telefono) {
            $rules['nuevo_telefono'] = 'required|numeric|digits_between:8,15';
        }

        // Si falta email, es opcional pero debe ser válido
        if ($this->pedir_email) {
            $rules['nuevo_email'] = 'nullable|email|max:100';
        }

        $this->validate($rules, [
            'nuevo_telefono.required' => 'El número de celular es obligatorio para registrar el pago.',
            'nuevo_email.email' => 'El formato del correo no es válido.',
        ]);

        $concepto = Concepto::find($this->concepto_id);
        $user = Auth::user();

        // Restricción Rol Cajero Colegio
        if ($user->isUsuarioColegio() && $concepto->nombre !== 'Aporte Colegio') {
            $this->addError('concepto_id', 'No tienes permisos para cobrar este concepto.');

            return;
        }

        // 2. ACTUALIZAR DATOS DEL SOCIO (Si aplica)
        if ($this->pedir_telefono || ($this->pedir_email && $this->nuevo_email)) {
            $datosActualizar = [];
            if ($this->pedir_telefono) {
                $datosActualizar['telefono'] = $this->nuevo_telefono;
            }
            if ($this->pedir_email && $this->nuevo_email) {
                $datosActualizar['email'] = $this->nuevo_email;
            }

            $this->socioSeleccionado->update($datosActualizar);
        }

        // 3. Validar Gates
        $idColegioDestino = str_contains(strtolower($concepto->nombre), 'colegio')
            ? $this->socioSeleccionado->id_colegio
            : null;

        if (! Gate::allows('registrar-pago', $idColegioDestino)) {
            $this->addError('permiso', 'NO TIENES AUTORIZACIÓN para cobrar en esta entidad.');

            return;
        }

        // 4. Crear Pago
        Pago::create([
            'id_socio' => $this->socioSeleccionado->id,
            'id_concepto' => $this->concepto_id,
            'id_usuario' => Auth::id(),
            'id_metodo_pago' => $this->metodo_pago_id,
            'monto_pagado' => $this->monto_total,
            'nro_transaccion' => $this->nro_transaccion,
            'fecha_pago' => now(),
            'periodo_inicio' => $this->usar_periodos ? $this->fecha_inicio : null,
            'periodo_fin' => $this->usar_periodos ? Carbon::parse($this->fecha_fin)->endOfMonth()->format('Y-m-d') : null,
            'estado' => 'aprobado',
            'observacion' => $this->observacion,
        ]);

        session()->flash('success', 'Pago registrado y datos de socio actualizados correctamente.');
        $this->cancelarSeleccion();
    }

    private function resetFormularioPago()
    {
        $this->reset([
            'concepto_id', 'metodo_pago_id', 'nro_transaccion', 'observacion',
            'monto_total', 'tarifa_unitaria', 'nuevo_telefono', 'nuevo_email',
            'pedir_telefono', 'pedir_email',
        ]);
        $this->mount();
    }

    public function render()
    {
        $user = Auth::user();
        $queryConceptos = Concepto::query();

        if ($user->isUsuarioColegio()) {
            $queryConceptos->where('nombre', 'Aporte Colegio');
        }

        return view('livewire.admin.registrar-pago', [
            'conceptos' => $queryConceptos->get(),
            'metodos' => MetodoPago::where('activo', true)->get(),
        ]);
    }
}
