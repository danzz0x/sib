<?php

namespace App\Providers;

use App\Models\Colegio;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (str_contains(config('app.url'), 'https://')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    	}

        Gate::define('manage-everything', function (User $user) {
            return $user->role === User::ROLE_ADMIN;
        });

        Gate::define('registrar-pago', function (User $user, $colegioId = null) {

            // 1. NIVEL SUPREMO: Admin y Cajero General pueden cobrar todo
            if ($user->tieneAccesoGlobalPagos()) {
                return true;
            }

            // 2. RESTRICCIÓN: Si es un usuario de colegio...
            if ($user->isUsuarioColegio()) {

                // A. No puede cobrar conceptos Generales (SIB Central / null)
                if (is_null($colegioId)) {
                    return false;
                }

                // B. Solo puede cobrar en SU colegio si es Tesorero o Director
                return $user->tieneRolEnColegio($colegioId, [
                    User::PIVOT_ROL_TESORERO,
                    User::PIVOT_ROL_DIRECTOR,
                ]);
            }

            return false;
        });
        Gate::define('manage-settings', function (User $user) {
            return $user->isSuperAdmin() || $user->isCajeroGeneral();
        });
        Gate::define('access-operations', function (User $user) {
            // Si es Admin o Cajero Central, pasa
            if ($user->tieneAccesoGlobalPagos()) {
                return true;
            }

            // Si es usuario de colegio, verificamos si tiene roles operativos en CUALQUIER colegio
            if ($user->isUsuarioColegio()) {
                return $user->colegios()
                    ->wherePivotIn('tipo_usuario_colegio', [
                        User::PIVOT_ROL_DIRECTOR,
                        User::PIVOT_ROL_TESORERO,
                    ])->exists();
            }

            return false;
        });

        Gate::define('manage-post', function (User $user, ?Colegio $colegio = null) {

            // 1. Admin Global pasa siempre
            if ($user->isSuperAdmin()) {
                return true;
            }

            if (! $colegio) {
                // Define aquí qué pasa si no hay colegio.
                // Por ejemplo, si no es superAdmin y no hay colegio, denegar:
                return false;
            }
            // 2. Buscamos el colegio en la memoria del usuario
            $colegioPivot = $user->colegios->find($colegio->id);

            // --- CORRECCIÓN AQUÍ ---
            // 3. Verificamos SI EXISTE la relación antes de leer el pivot.
            // Si $colegioPivot es null, significa que el usuario NO pertenece a este colegio.
            // Por tanto, retornamos false inmediatamente.
            if (! $colegioPivot) {
                return false;
            }
            // -----------------------

            // 4. Ahora sí es seguro leer ->pivot
            return in_array($colegioPivot->pivot->tipo_usuario_colegio, [
                User::PIVOT_ROL_DIRECTOR,
                User::PIVOT_ROL_PUBLICADOR,
            ]);
        });
    }
}
