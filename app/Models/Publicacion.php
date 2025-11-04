<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\CommonMark\CommonMarkConverter;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'publicaciones';

    protected $fillable = [
        'seccion_id',
        'titulo',
        'descripcion',
        'imagen',
        'url',
        'prioridad',
        'archivado',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'detalles',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'archivado' => 'boolean',
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public static function getNextPriority($seccionId)
    {
        return self::where('seccion_id', $seccionId)->max('prioridad') + 1;
    }

    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/'.$this->imagen) : null;
    }

    protected static ?CommonMarkConverter $converterInstance = null;

    // ... otros métodos ...

    public function getDetallesHtmlAttribute(): ?string
    {
        if (! $this->detalles) {
            return null;
        }

        // 1. Inicializar el conversor una sola vez
        if (is_null(static::$converterInstance)) {
            // Puedes usar CommonMarkConverter o GithubFlavoredMarkdownConverter
            static::$converterInstance = new CommonMarkConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 6,
            ]);
        }

        // 2. Usar la instancia cacheadada para la conversión
        return static::$converterInstance->convert($this->detalles);
    }
}
