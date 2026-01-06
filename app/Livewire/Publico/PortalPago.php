<?php

namespace App\Livewire\Publico;

use App\Models\Concepto;
use App\Models\CuentaBancaria;
use App\Models\Pago;
use App\Models\Socio;
use App\Models\Tarifa;
use App\Models\User;
use App\Notifications\NuevoPagoPendiente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class PortalPago extends Component
{
    use WithFileUploads;

    public $colegioId;

    public $paso = 1;

    public $procesando = false;

    // Datos Socio
    public $rni;

    public $turnstileToken;

    public $socio;

    public $ultimoPagoMes;

    // Datos Contacto
    public $nuevo_telefono;

    public $nuevo_email;

    public $pedir_telefono = false;

    public $pedir_email = false;

    // Datos Pago
    public $monto;

    public $nro_transaccion;

    public $comprobante;

    // Logica
    public $concepto;

    public $cuentaBancaria;

    public $fecha_inicio;

    public $fecha_fin;

    public $listaMeses = [];

    public $tarifaBase = 0;

    public $cantidadMeses = 1;

    // Array asociativo: ['2024-01-01' => 'pendiente', '2024-02-01' => 'aprobado']
    public $mesesPagados = [];

    public function mount($idColegio = null)
    {
        $this->colegioId = $idColegio;
        $this->concepto = Concepto::where('nombre', 'Aporte Colegio')->first();

        // 1. GENERACIÓN DE LISTA (SOLO FUTURO)
        $fecha = now()->startOfMonth();

        // Generamos 24 meses hacia adelante
        for ($i = 0; $i < 24; $i++) {
            $clave = $fecha->format('Y-m-d');
            $texto = ucfirst($fecha->locale('es')->monthName).' '.$fecha->year;

            $this->listaMeses[$clave] = [
                'texto' => $texto,
                'fecha' => $fecha->copy(),
            ];
            $fecha->addMonth();
        }

        // Valores por defecto
        $hoy = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;
    }

    public function buscarSocio()
    {
        $this->validate([
            'rni' => 'required|string|min:3',
            'turnstileToken' => ['required', new Turnstile],
        ]);

        $this->socio = Socio::with(['tipoSocio', 'colegio'])->where('rni', $this->rni)->first();

        if (! $this->socio) {
            $this->addError('rni', 'No encontramos un socio con este documento.');
            $this->resetCaptcha();

            return;
        }

        // 2. CARGAR HISTORIAL
        $this->mesesPagados = [];

        $pagos = Pago::where('id_socio', $this->socio->id)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->get();

        foreach ($pagos as $pago) {
            $inicio = Carbon::parse($pago->periodo_inicio)->startOfMonth();
            $fin = Carbon::parse($pago->periodo_fin)->startOfMonth();

            while ($inicio <= $fin) {
                $this->mesesPagados[$inicio->format('Y-m-d')] = $pago->estado;
                $inicio->addMonth();
            }
        }

        // 3. INFO VISUAL
        $ultimo = Pago::where('id_socio', $this->socio->id)
            ->where('estado', 'aprobado')
            ->orderBy('periodo_fin', 'desc')
            ->first();

        $this->ultimoPagoMes = $ultimo
            ? ucfirst(Carbon::parse($ultimo->periodo_fin)->locale('es')->monthName).' '.Carbon::parse($ultimo->periodo_fin)->year
            : 'Sin pagos registrados';

        // 4. PRESELECCIÓN INTELIGENTE
        $sugerencia = now()->startOfMonth();
        while (array_key_exists($sugerencia->format('Y-m-d'), $this->mesesPagados)) {
            $sugerencia->addMonth();
        }

        $this->fecha_inicio = $sugerencia->format('Y-m-d');
        $this->fecha_fin = $sugerencia->format('Y-m-d');

        $this->pedir_telefono = empty($this->socio->telefono);
        $this->pedir_email = empty($this->socio->email);

        $this->cuentaBancaria = CuentaBancaria::where('activo', true)
            ->where(function ($q) {
                $q->where('id_colegio', $this->socio->id_colegio)->orWhereNull('id_colegio');
            })->orderByDesc('id_colegio')->first();

        $tarifa = Tarifa::buscarPrecio($this->concepto->id, $this->socio->id_tipo_socio, $this->socio->id_colegio)->first();
        $this->tarifaBase = $tarifa ? $tarifa->monto : 0;

        $this->calcularTotal();
        $this->paso = 2;
    }

    public function updatedFechaInicio()
    {
        // CORRECCIÓN 1: Limpiar errores visuales inmediatamente al cambiar la fecha
        $this->resetValidation();

        if ($this->fecha_fin < $this->fecha_inicio) {
            $this->fecha_fin = $this->fecha_inicio;
        }
        $this->calcularTotal();
    }

    public function updatedFechaFin()
    {
        // CORRECCIÓN 1: Limpiar errores visuales inmediatamente al cambiar la fecha
        $this->resetValidation();

        if ($this->fecha_fin < $this->fecha_inicio) {
            $this->fecha_inicio = $this->fecha_fin;
        }
        $this->calcularTotal();
    }

    public function calcularTotal()
    {
        $inicio = Carbon::parse($this->fecha_inicio);
        $fin = Carbon::parse($this->fecha_fin);

        $this->cantidadMeses = $inicio->diffInMonths($fin) + 1;
        $this->monto = number_format($this->tarifaBase * $this->cantidadMeses, 2, '.', '');
    }

    public function procesarPago()
    {
        // CORRECCIÓN 2: Limpiar validaciones previas al intentar procesar de nuevo
        $this->resetValidation();

        if ($this->procesando) {
            return;
        }
        $this->procesando = true;

        // --- VALIDACIONES DE SEGURIDAD (BACKEND) ---

        // 1. Validar que no se paguen meses anteriores al actual
        $inicioMesActual = now()->startOfMonth()->format('Y-m-d');
        if ($this->fecha_inicio < $inicioMesActual) {
            $this->addError('fecha_inicio', 'No puedes seleccionar una fecha pasada. El sistema solo permite aportes desde el mes actual en adelante.');
            $this->procesando = false;

            return;
        }

        // 2. Generar el rango de meses que intenta pagar
        $mesesAIntentar = [];
        $iterador = Carbon::parse($this->fecha_inicio)->startOfMonth();
        $finIntento = Carbon::parse($this->fecha_fin)->startOfMonth();

        while ($iterador <= $finIntento) {
            $mesesAIntentar[] = $iterador->format('Y-m-d');
            $iterador->addMonth();
        }

        // 3. Verificar colisión con meses ya pagados/pendientes
        // CORRECCIÓN 3: Mensajes de error mucho más claros identificando el mes culpable
        $mesesConflictivos = array_intersect($mesesAIntentar, array_keys($this->mesesPagados));

        if (! empty($mesesConflictivos)) {
            // Obtenemos el primer mes que da error para mostrárselo al usuario
            $primerConflicto = reset($mesesConflictivos);
            $fechaConflicto = Carbon::parse($primerConflicto);
            $nombreMes = ucfirst($fechaConflicto->locale('es')->monthName).' '.$fechaConflicto->year;
            $estado = $this->mesesPagados[$primerConflicto]; // 'pendiente' o 'aprobado'

            $msg = $estado === 'pendiente'
                ? "El mes de {$nombreMes} ya tiene un pago en proceso de validación."
                : "El mes de {$nombreMes} ya está pagado. Por favor selecciona otro rango.";

            $this->addError('fecha_inicio', $msg);
            $this->procesando = false;

            return;
        }

        $rules = [
            'nro_transaccion' => 'required|string|min:4|unique:pagos,nro_transaccion',
            'comprobante' => 'required|image|max:5120',
            'fecha_inicio' => 'required|date',
        ];

        // Mensajes personalizados para el validador estándar
        $messages = [
            'nro_transaccion.required' => 'El número de comprobante es obligatorio.',
            'nro_transaccion.unique' => 'Este número de comprobante ya fue registrado anteriormente.',
            'comprobante.required' => 'Debes subir la foto del comprobante.',
            'comprobante.image' => 'El archivo debe ser una imagen (JPG, PNG).',
            'comprobante.max' => 'La imagen es muy pesada (Máx 5MB).',
        ];

        if ($this->pedir_telefono) {
            $rules['nuevo_telefono'] = 'required|numeric|digits_between:7,15';
            $messages['nuevo_telefono.required'] = 'Necesitamos tu celular para contactarte.';
        }
        if ($this->pedir_email) {
            $rules['nuevo_email'] = 'nullable|email|max:100';
            $messages['nuevo_email.email'] = 'El correo electrónico no es válido.';
        }

        // Ejecutar validación estándar con mensajes personalizados
        try {
            $this->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si falla la validación, liberamos el bloqueo de proceso
            $this->procesando = false;
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Actualizar datos de contacto si el usuario los proveyó
            if ($this->pedir_telefono || ($this->pedir_email && $this->nuevo_email)) {
                $this->socio->update(array_filter([
                    'telefono' => $this->nuevo_telefono ?: $this->socio->telefono,
                    'email' => $this->nuevo_email ?: $this->socio->email,
                ]));
            }

            $path = $this->comprobante->store('comprobantes', 'public');

            $pago = Pago::create([
                'id_socio' => $this->socio->id,
                'id_concepto' => $this->concepto->id,
                'id_metodo_pago' => 2, // Transferencia / QR
                'monto_pagado' => $this->monto,
                'nro_transaccion' => $this->nro_transaccion,
                'fecha_pago' => now(),
                'periodo_inicio' => $this->fecha_inicio,
                'periodo_fin' => Carbon::parse($this->fecha_fin)->endOfMonth()->format('Y-m-d'),
                'estado' => 'pendiente',
                'comprobante_path' => $path,
            ]);

            // Notificación
            $destinatarios = User::where('role', 'administrador')
                ->orWhereHas('colegios', fn ($q) => $q->where('colegio_id', $this->socio->id_colegio))
                ->get()->unique('id');

            Notification::send($destinatarios, new NuevoPagoPendiente($pago));

            DB::commit();
            $this->paso = 3;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('nro_transaccion', 'Error del sistema: '.$e->getMessage());
        } finally {
            $this->procesando = false;
        }
    }

    public function resetear()
    {
        $this->reset([
            'rni', 'socio', 'monto', 'nro_transaccion', 'comprobante',
            'paso', 'turnstileToken', 'nuevo_telefono', 'nuevo_email',
            'pedir_telefono', 'pedir_email', 'mesesPagados', 'procesando',
            'ultimoPagoMes',
        ]);
        $this->mount($this->colegioId);
        $this->resetCaptcha();
        $this->resetValidation(); // Limpieza extra al resetear
    }

    public function cerrarModal()
    {
        $this->resetear();
        $this->dispatch('close-modal');
    }

    public function resetCaptcha()
    {
        $this->turnstileToken = null;
        $this->dispatch('reset-turnstile');
    }

    public function render()
    {
        return view('livewire.publico.portal-pago');
    }
}
