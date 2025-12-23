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

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('dashboard', Colegios::class)->name('dashboard')->middleware('can:manage-everything');
    Route::get('admin/caja', RegistrarPago::class)->name('admin.pagos');
    Route::get('admin/users', ManageUsers::class)->name('admin.users')->middleware('can:manage-everything');
    Route::get('admin/tarifas', ManageTarifas::class)->name('admin.tarifas');
    Route::get('admin/socios', ManageSocios::class)->name('admin.socios');
    Route::get('admin/hitorial-pagos', HistorialPagos::class)->name('admin.historial');
    Route::get('admin/cuentas', ManageCuentas::class)->name('admin.cuentas');
    Route::get('admin/validar', ValidarPagos::class)->name('admin.validar');
});

Route::get('colegios/{slug}', ShowColegio::class)->name('colegios.show');
