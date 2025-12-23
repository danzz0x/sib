<?php

use Illuminate\Support\Facades\Broadcast;

// Permiso para escuchar notificaciones privadas del propio usuario
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Permiso para el canal de un colegio específico (si lo usamos después)
Broadcast::channel('colegio.{id}', function ($user, $id) {
    // El usuario puede escuchar si es Admin Global O si pertenece a ese colegio
    return $user->isAdmin() || $user->colegios->contains('id', $id);
});
