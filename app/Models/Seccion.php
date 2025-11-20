<?php

namespace App\Models;

use App\Enums\TipoMostrarSeccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'colegio_id',
        'titulo',
        'tipo_mostrar',
        'anio',
        'orden',
    ];

    protected $casts = [
        'tipo_mostrar' => TipoMostrarSeccion::class,
    ];

    public function colegio()
    {
        return $this->belongsTo(Colegio::class);
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class)->orderBy('prioridad', 'desc');
    }

    public function publicacionesActivas()
    {
        return $this->hasMany(Publicacion::class)
            ->where('archivado', false)
            ->orderBy('prioridad', 'desc');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('orden');
    }

    public static function getNextOrder($colegioId)
    {
        return self::where('colegio_id', $colegioId)->max('orden') + 1;
    }

    public function tieneTitulo(): bool
    {
        return ! empty($this->titulo);
    }

    public function getTituloMostrarAttribute(): string
    {
        return $this->titulo ?: 'Sección '.ucfirst($this->tipo_mostrar->value);
    }
}
