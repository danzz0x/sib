<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concepto extends Model
{
    protected $table = 'conceptos';

    protected $fillable = ['nombre', 'es_periodico'];

    protected $casts = ['es_periodico' => 'boolean'];
}
