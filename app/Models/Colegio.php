<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Colegio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'logo',
        'descripcion',
        'activo',
    ];

    // Cache de atributos calculados
    protected $appends = ['logo_url', 'descripcion_corta', 'inicial'];

    // Generar slug automáticamente

    protected static function booted()
    {
        static::creating(function ($colegio) {
            if (empty($colegio->slug)) {
                $colegio->slug = static::extraerSlug($colegio->nombre);
            }
        });

        static::updating(function ($colegio) {
            if ($colegio->isDirty('nombre')) {
                $colegio->slug = static::extraerSlug($colegio->nombre);
            }
        });
    }

    protected static function extraerSlug($nombre)
    {
        // Extrae el texto dentro de los paréntesis
        if (preg_match('/\(([^)]+)\)/', $nombre, $matches)) {
            return Str::slug($matches[1]);
        }

        // Si no tiene paréntesis, usa todo el nombre como fallback
        return Str::slug($nombre);
    }

    // Accessor optimizado para URL del logo
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset("storage/{$this->logo}") : null;
    }

    // Accessor para descripción corta
    public function getDescripcionCortaAttribute()
    {
        return $this->descripcion ? Str::limit($this->descripcion, 50) : '';
    }

    // Accessor para inicial
    public function getInicialAttribute()
    {
        return strtoupper(substr($this->nombre, 0, 1));
    }

    // Scope para optimizar consultas
    public function scopeConLogo($query)
    {
        return $query->select(['id', 'nombre', 'descripcion', 'logo', 'created_at']);
    }

    // === RELACIONES ===

    /**
     * Relación con contactos
     */
    public function contactos()
    {
        return $this->hasMany(ColegioContacto::class);
    }

    /**
     * Relación con secciones ordenadas
     */
    public function secciones()
    {
        return $this->hasMany(Seccion::class)->orderBy('orden');
    }

    /**
     * Relación con publicaciones a través de secciones
     */
    public function publicaciones()
    {
        return $this->hasManyThrough(
            Publicacion::class,
            Seccion::class,
            'colegio_id', // Clave foránea en secciones
            'seccion_id', // Clave foránea en publicaciones
            'id', // Clave local en colegios
            'id'  // Clave local en secciones
        );
    }

    /**
     * Scope para colegios activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function getNombreLimpioAttribute(): string
    {
        $nombreLimpio = strtok($this->nombre, '(');

        return trim($nombreLimpio);
    }
}
