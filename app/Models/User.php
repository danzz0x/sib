<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    // 1. CONSTANTES DE ROLES GLOBALES
    const ROLE_ADMIN = 'administrador';

    const ROLE_CAJERO = 'cajero';

    const ROLE_COLEGIO = 'colegio';

    // 2. CONSTANTES DE ROLES DENTRO DEL COLEGIO (Pivot)
    const PIVOT_ROL_TESORERO = 'tesorero';

    const PIVOT_ROL_PUBLICADOR = 'publicador';

    const PIVOT_ROL_DIRECTOR = 'director';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function colegios()
    {
        return $this->belongsToMany(Colegio::class)->withPivot('tipo_usuario_colegio');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // ¿Es cajero general (SIB Nacional)?
    public function isCajeroGeneral(): bool
    {
        return $this->role === self::ROLE_CAJERO;
    }

    // ¿Tiene acceso global a cobros? (Admin + Cajero General)
    public function tieneAccesoGlobalPagos(): bool
    {
        return $this->isSuperAdmin() || $this->isCajeroGeneral();
    }

    // ¿Es usuario restringido a colegios específicose
    public function isUsuarioColegio(): bool
    {
        return $this->role === self::ROLE_COLEGIO;
    }

    // Verifica si tiene un ROL específico DENTRO de un colegio específico
    public function tieneRolEnColegio($colegioId, array $rolesPermitidos): bool
    {
        // Usamos wherePivotIn y exists para una consulta SQL optimizada (devuelve true/false)
        return $this->colegios()
            ->where('colegio_id', $colegioId)
            ->wherePivotIn('tipo_usuario_colegio', $rolesPermitidos)
            ->exists();
    }
}
