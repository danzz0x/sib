<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tarifa extends Model
{
    protected $table = 'tarifas';

    protected $fillable = ['id_concepto', 'id_tipo_socio', 'id_colegio', 'monto'];

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(Concepto::class, 'id_concepto');
    }

    public function tipoSocio(): BelongsTo
    {
        return $this->belongsTo(TipoSocio::class, 'id_tipo_socio');
    }

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class, 'id_colegio');
    }

    public function scopeBuscarPrecio(Builder $query, $conceptoId, $tipoSocioId, $colegioId)
    {
        return $query->where('id_concepto', $conceptoId)
            ->where('id_tipo_socio', $tipoSocioId)
            ->where(function ($q) use ($colegioId) {
                $q->where('id_colegio', $colegioId)->orWhereNull('id_colegio');
            })
            ->orderBy('id_colegio', 'desc');
    }
}
