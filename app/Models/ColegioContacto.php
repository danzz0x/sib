<?php

namespace App\Models;

use App\Enums\TipoContacto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColegioContacto extends Model
{
    use HasFactory;

    protected $fillable = [
        'colegio_id',
        'tipo',
        'valor',
    ];

    protected $casts = [
        'tipo'  => TipoContacto::class,
    ];

    public function colegio() {
        return $this->belongsTo(Colegio::class);
    }
}
