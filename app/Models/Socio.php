<?php

namespace App\Models;

use App\Enums\EspecialidadEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Socio extends Model
{
    use Notifiable;

    protected $table = 'socios';

    protected $fillable = [
        'id_tipo_socio',
        'id_colegio',
        'nombre',
        'cedula',
        'rni',
        'especialidad',
        'email',
        'telefono',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'especialidad' => EspecialidadEnum::class,
    ];

    public function tipoSocio()
    {
        return $this->belongsTo(TipoSocio::class, 'id_tipo_socio');
    }

    public function colegio()
    {
        return $this->belongsTo(Colegio::class, 'id_colegio');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_socio');
    }
}
