<?php

namespace App\Livewire\Admin;

use App\Models\Concepto;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Socio;
use App\Models\Tarifa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class RegistrarPago extends Component
{
    // --- Búsqueda ---
    public $search = '';

    public $resultados = [];

    public $socioSeleccionado = null;

    public $ultimoPagoMes;

    // --- Datos de Contacto ---
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

    public $usar_periodos = false;

    public $fecha_inicio;

    public $fecha_fin;

    public $listaMeses = [];

    // Array asociativo: ['2024-01-01' => 'aprobado', '2024-02-01' => 'pendiente']
    public $mesesPagados = [];

    public function mount()
    {
        $hoy = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;

        // Generamos lista de meses: Desde el mes actual hasta 2 años a futuro
        $inicio = now()->startOfMonth();
        for ($i = 0; $i < 24; $i++) {
            $fecha = $inicio->copy()->addMonths($i);
            // Clave estricta Y-m-d para coincidir con la base de datos
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
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                    ->orWhere('rni', 'like', "%{$this->search}%")
                    ->orWhere('cedula', 'like', "%{$this->search}%");
            })
            ->limit(5)
            ->get();
    }

    public function seleccionarSocio($id)
    {
        $this->socioSeleccionado = Socio::with('tipoSocio', 'colegio')->find($id);
        $this->search = '';
        $this->resultados = [];

        $this->resetFormularioPago(false);

        $this->pedir_telefono = empty($this->socioSeleccionado->telefono);
        $this->pedir_email = empty($this->socioSeleccionado->email);

        $user = Auth::user();
        if ($user->isUsuarioColegio()) {
            $conceptoAporte = Concepto::where('nombre', 'Aporte Colegio')->first();
            if ($conceptoAporte) {
                $this->concepto_id = $conceptoAporte->id;
                $this->updatedConceptoId();
            }
        }

        if (! $this->concepto_id) {
            $this->mesesPagados = [];
            $this->ultimoPagoMes = 'Seleccione un concepto';
        }
    }

    private function cargarHistorialSocio()
    {
        if (! $this->socioSeleccionado || ! $this->concepto_id) {
            $this->mesesPagados = [];
            $this->ultimoPagoMes = 'Seleccione un concepto';

            return;
        }

        $this->mesesPagados = [];

        $pagos = Pago::where('id_socio', $this->socioSeleccionado->id)
            ->where('id_concepto', $this->concepto_id) // <--- ESTA LINEA ARREGLA EL PROBLEMA
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->get();

        foreach ($pagos as $pago) {
            $inicio = Carbon::parse($pago->periodo_inicio)->startOfMonth()->startOfDay();
            $fin = Carbon::parse($pago->periodo_fin)->startOfMonth()->startOfDay();

            while ($inicio <= $fin) {
                $clave = $inicio->format('Y-m-d');

                if (! isset($this->mesesPagados[$clave]) || $this->mesesPagados[$clave] !== 'aprobado') {
                    $this->mesesPagados[$clave] = $pago->estado;
                }

                $inicio->addMonth();
            }
        }

        $ultimo = Pago::where('id_socio', $this->socioSeleccionado->id)
            ->where('id_concepto', $this->concepto_id)
            ->where('estado', 'aprobado')
            ->orderBy('periodo_fin', 'desc')
            ->first();

        $this->ultimoPagoMes = $ultimo
            ? ucfirst(Carbon::parse($ultimo->periodo_fin)->locale('es')->monthName).' '.Carbon::parse($ultimo->periodo_fin)->year
            : 'Sin pagos registrados para este concepto';
    }

    public function updatedConceptoId()
    {
        $this->resetValidation();

        if (! $this->socioSeleccionado || ! $this->concepto_id) {
            return;
        }

        $this->cargarHistorialSocio();

        $concepto = Concepto::find($this->concepto_id);
        $this->usar_periodos = $concepto->es_periodico;

        $destinoColegioId = str_contains(strtolower($concepto->nombre), 'colegio')
            ? $this->socioSeleccionado->id_colegio
            : null;

        $tarifa = Tarifa::buscarPrecio($this->concepto_id, $this->socioSeleccionado->id_tipo_socio, $destinoColegioId)->first();

        $this->tarifa_unitaria = $tarifa ? $tarifa->monto : 0;

        if (! $tarifa) {
            $this->addError('concepto_id', 'No existe una tarifa configurada para este socio y concepto.');
        }

        if ($this->usar_periodos) {
            $sugerencia = now()->startOfMonth();

            while (array_key_exists($sugerencia->format('Y-m-d'), $this->mesesPagados)) {
                $sugerencia->addMonth();
            }
            $this->fecha_inicio = $sugerencia->format('Y-m-d');
            $this->fecha_fin = $sugerencia->format('Y-m-d');
        }

        $this->calcularTotal();
    }

    public function updatedFechaInicio()
    {
        $this->resetValidation();
        $this->validarFechas();
        $this->calcularTotal();
    }

    public function updatedFechaFin()
    {
        $this->resetValidation();
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
        $this->resetValidation();

        $rules = [
            'socioSeleccionado' => 'required',
            'concepto_id' => 'required|exists:conceptos,id',
            'metodo_pago_id' => 'required|exists:metodos_pago,id',
            'monto_total' => 'required|numeric|min:0.1',
            'nro_transaccion' => 'nullable|string|unique:pagos,nro_transaccion',
        ];

        $messages = [
            'concepto_id.required' => 'Debe seleccionar un concepto de pago.',
            'metodo_pago_id.required' => 'Seleccione cómo realizó el pago.',
            'monto_total.min' => 'El monto no puede ser cero.',
            'nro_transaccion.unique' => 'Este número de comprobante ya está registrado.',
            'nuevo_telefono.required' => 'El teléfono es obligatorio para este socio.',
            'nuevo_email.email' => 'El formato del correo es inválido.',
        ];

        if ($this->pedir_telefono) {
            $rules['nuevo_telefono'] = 'required|numeric|digits_between:7,15';
        }
        if ($this->pedir_email) {
            $rules['nuevo_email'] = 'nullable|email';
        }

        $this->validate($rules, $messages);

        // Validación de meses ocupados (Ahora funciona correctamente con el filtro de concepto)
        if ($this->usar_periodos) {
            $mesesAIntentar = [];
            $iterador = Carbon::parse($this->fecha_inicio)->startOfMonth();
            $finIntento = Carbon::parse($this->fecha_fin)->startOfMonth();

            while ($iterador <= $finIntento) {
                $mesesAIntentar[] = $iterador->format('Y-m-d');
                $iterador->addMonth();
            }

            // Usamos array_intersect con las KEYS del array asociativo
            $conflicto = array_intersect($mesesAIntentar, array_keys($this->mesesPagados));

            if (! empty($conflicto)) {
                $primerMes = Carbon::parse(reset($conflicto));
                $nombreMes = ucfirst($primerMes->locale('es')->monthName).' '.$primerMes->year;

                $this->addError('fecha_inicio', "El mes de {$nombreMes} ya está pagado (o pendiente) para este concepto. Verifique el rango.");

                return;
            }
        }

        $conceptoObj = Concepto::find($this->concepto_id);
        $idColegioDestino = str_contains(strtolower($conceptoObj->nombre), 'colegio')
            ? $this->socioSeleccionado->id_colegio
            : null;

        if (! Gate::allows('registrar-pago', $idColegioDestino)) {
            $this->addError('permiso', 'No tienes autorización para registrar pagos en esta entidad.');

            return;
        }

        try {
            DB::beginTransaction();

            if ($this->pedir_telefono || ($this->pedir_email && $this->nuevo_email)) {
                $this->socioSeleccionado->update(array_filter([
                    'telefono' => $this->nuevo_telefono ?: $this->socioSeleccionado->telefono,
                    'email' => $this->nuevo_email ?: $this->socioSeleccionado->email,
                ]));
            }

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
                'estado' => 'aprobado', // Pago en ventanilla es aprobado directo
                'observacion' => $this->observacion,
            ]);

            DB::commit();
            session()->flash('success', 'Pago registrado exitosamente.');
            $this->cancelarSeleccion();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('monto_total', 'Error crítico al procesar: '.$e->getMessage());
        }
    }

    private function resetFormularioPago($limpiarSocio = true)
    {
        if ($limpiarSocio) {
            $this->socioSeleccionado = null;
            // Si limpiamos el socio, limpiamos el historial
            $this->mesesPagados = [];
            $this->ultimoPagoMes = null;
        }

        $this->reset([
            'concepto_id', 'metodo_pago_id', 'nro_transaccion', 'observacion',
            'monto_total', 'tarifa_unitaria', 'nuevo_telefono', 'nuevo_email',
            'pedir_telefono', 'pedir_email', 'usar_periodos',
        ]);

        // No llamamos a mount() completo para no resetear fechas si no es necesario,
        // pero reseteamos validaciones
        $this->resetValidation();

        $hoy = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;
    }

    public function cancelarSeleccion()
    {
        $this->resetFormularioPago(true);
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
