<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoSocio extends Model
{
    protected $table = 'tipos_socio';

    protected $fillable = ['nombre'];
}
