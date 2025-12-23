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
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\WithFileUploads;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class PortalPago extends Component
{
    use WithFileUploads;

    public $colegioId;

    public $paso = 1;

    // Datos Socio
    public $rni;

    public $turnstileToken;

    public $socio;

    // Datos Contacto (NUEVO)
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

    public function mount($idColegio = null)
    {
        $this->colegioId = $idColegio;
        $this->concepto = Concepto::where('nombre', 'Aporte Colegio')->first();

        $fecha = now()->startOfMonth();
        for ($i = 0; $i < 24; $i++) {
            $clave = $fecha->format('Y-m-d');
            $texto = ucfirst($fecha->locale('es')->monthName).' '.$fecha->year;
            $this->listaMeses[$clave] = $texto;
            $fecha->addMonth();
        }

        $hoy = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_inicio = $hoy;
        $this->fecha_fin = $hoy;
    }

    public function buscarSocio()
    {
        $this->validate([
            'rni' => 'required|string|min:3',
            'turnstileToken' => ['required', new Turnstile],
        ], [
            'turnstileToken.required' => 'Por favor completa la verificación de seguridad.',
            'rni.required' => 'Debes ingresar tu RNI',
        ]);

        $this->socio = Socio::with('tipoSocio', 'colegio')->where('rni', $this->rni)->first();

        if (! $this->socio) {
            $this->addError('rni', 'No encontramos ningún socio registrado con este RNI.');
            $this->resetCaptcha();

            return;
        }

        // --- LÓGICA DE DATOS FALTANTES ---
        $this->pedir_telefono = empty($this->socio->telefono);
        $this->pedir_email = empty($this->socio->email);
        $this->nuevo_telefono = '';
        $this->nuevo_email = '';

        // --- RESTO DE TU LÓGICA EXISTENTE ---
        $this->cuentaBancaria = CuentaBancaria::where('activo', true)
            ->where(function ($q) {
                $q->where('id_colegio', $this->socio->id_colegio)
                    ->orWhereNull('id_colegio');
            })
            ->orderByDesc('id_colegio')
            ->first();

        $tarifa = Tarifa::buscarPrecio(
            $this->concepto->id,
            $this->socio->id_tipo_socio,
            $this->socio->id_colegio
        )->first();

        $this->tarifaBase = $tarifa ? $tarifa->monto : 0;
        $this->calcularTotal();

        $this->paso = 2;
    }

    // ... updatedFechaInicio, updatedFechaFin, calcularTotal (IGUAL QUE ANTES) ...
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
        if ($this->tarifaBase > 0) {
            $inicio = Carbon::parse($this->fecha_inicio);
            $fin = Carbon::parse($this->fecha_fin);
            $this->cantidadMeses = $inicio->diffInMonths($fin) + 1;
            $this->monto = number_format($this->tarifaBase * $this->cantidadMeses, 2, '.', '');
        } else {
            $this->monto = 0;
        }
    }

    public function procesarPago()
    {
        // 1. Reglas Base
        $rules = [
            'monto' => 'required|numeric|min:1',
            'nro_transaccion' => 'required|string|min:4',
            'comprobante' => 'required|image|max:5120',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ];

        // 2. Reglas Dinámicas (Contacto)
        if ($this->pedir_telefono) {
            $rules['nuevo_telefono'] = 'required|numeric|digits_between:8,15';
        }
        if ($this->pedir_email) {
            $rules['nuevo_email'] = 'nullable|email|max:100';
        }

        $this->validate($rules, [
            'nuevo_telefono.required' => 'Necesitamos tu celular para enviarte el recibo.',
            'comprobante.max' => 'La imagen es muy pesada (Máx 5MB).',
            // ... otros mensajes ...
        ]);

        // 3. Actualizar Socio si aplica
        if ($this->pedir_telefono || ($this->pedir_email && $this->nuevo_email)) {
            $datosActualizar = [];
            if ($this->pedir_telefono) {
                $datosActualizar['telefono'] = $this->nuevo_telefono;
            }
            if ($this->pedir_email && $this->nuevo_email) {
                $datosActualizar['email'] = $this->nuevo_email;
            }

            $this->socio->update($datosActualizar);
        }

        // 4. Guardar Pago
        $path = $this->comprobante->store('comprobantes', 'public');
        $periodoFinReal = Carbon::parse($this->fecha_fin)->endOfMonth()->format('Y-m-d');

        $pago = Pago::create([
            'id_socio' => $this->socio->id,
            'id_concepto' => $this->concepto->id,
            'id_metodo_pago' => 2,
            'id_usuario' => null,
            'monto_pagado' => $this->monto,
            'nro_transaccion' => $this->nro_transaccion,
            'fecha_pago' => now(),
            'periodo_inicio' => $this->fecha_inicio,
            'periodo_fin' => $periodoFinReal,
            'estado' => 'pendiente',
            'comprobante_path' => $path,
        ]);

        $admins = User::where('role', User::ROLE_ADMIN)->get();

        // Buscar Cajeros del colegio específico (si aplica)
        if ($this->socio->id_colegio) {
            $cajerosColegio = User::whereHas('colegios', function ($q) {
                $q->where('colegio_id', $this->socio->id_colegio)
                    ->whereIn('tipo_usuario_colegio', ['tesorero', 'director']);
            })->get();

            // Unir colecciones
            $destinatarios = $admins->merge($cajerosColegio);
        } else {
            $destinatarios = $admins;
        }

        // Enviar notificación masiva (Database + Broadcast)
        Notification::send($destinatarios, new NuevoPagoPendiente($pago));

        $this->paso = 3;
    }

    // ... resetear, cerrarModal, resetCaptcha (IGUAL QUE ANTES) ...
    public function resetear()
    {
        $this->reset(['rni', 'socio', 'monto', 'nro_transaccion', 'comprobante', 'paso', 'turnstileToken', 'nuevo_telefono', 'nuevo_email', 'pedir_telefono', 'pedir_email']);
        $this->mount();
        $this->resetCaptcha();
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
