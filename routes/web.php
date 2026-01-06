<?php

use App\Livewire\Admin\Cuentas\ManageCuentas;
use App\Livewire\Admin\Pagos\HistorialPagos;
use App\Livewire\Admin\Pagos\ValidarPagos;
use App\Livewire\Admin\RegistrarPago;
use App\Livewire\Admin\SocioManagement\ManageSocios;
use App\Livewire\Admin\TarifaManagement\ManageTarifas;
use App\Livewire\Admin\UserManagement\ManageUsers;
use App\Livewire\Colegios;
use App\Livewire\Colegios\ShowColegio;
use Illuminate\Support\Facades\Route;

route::get('/', function () {

    return view('auth.login');

});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('dashboard', colegios::class)->name('dashboard');

    // GRUPO 1: Solo Administradores (Configuración Global)
    Route::middleware(['can:manage-settings'])->group(function () {
        Route::get('admin/tarifas', managetarifas::class)->name('admin.tarifas');
        Route::get('admin/cuentas', managecuentas::class)->name('admin.cuentas');
        Route::get('admin/users', manageusers::class)->name('admin.users')->middleware('can:manage-everything');
    });

    // GRUPO 2: Operaciones (Admin, Cajero Central y Personal de Colegio autorizado)
    Route::middleware(['can:access-operations'])->group(function () {
        Route::get('admin/caja', registrarpago::class)->name('admin.pagos');
        Route::get('admin/socios', managesocios::class)->name('admin.socios');
        Route::get('admin/hitorial-pagos', historialpagos::class)->name('admin.historial');
        Route::get('admin/validar', validarpagos::class)->name('admin.validar');
    });
});

Route::get('colegios/{slug}', ShowColegio::class)->name('colegios.show');
