<?php

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
    Route::get('dashboard', Colegios::class)->name('dashboard');

});

Route::get('colegios/{slug}', ShowColegio::class)->name('colegios.show');
