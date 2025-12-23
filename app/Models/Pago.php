<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'id_socio', 'id_concepto', 'id_usuario', 'id_metodo_pago',
        'monto_pagado', 'nro_transaccion', 'comprobante_path',
        'fecha_pago', 'periodo_inicio', 'periodo_fin', 'estado', 'observacion',
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'monto_pagado' => 'decimal:2',
    ];

    public function socio()
    {
        return $this->belongsTo(Socio::class, 'id_socio');
    }

    public function concepto()
    {
        return $this->belongsTo(Concepto::class, 'id_concepto');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'id_metodo_pago');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
