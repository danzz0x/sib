<?php

// app/Traits/WithAlerts.php

namespace App\Traits;

trait WithAlerts
{
    /**
     * Dispatch success alert
     */
    public function alertSuccess($title, $text = '', $options = [])
    {
        $this->dispatch('swal:success',
            title: $title,
            text: $text,
            options: $options
        );
    }

    /**
     * Dispatch error alert
     */
    public function alertError($title, $text = '', $options = [])
    {
        $this->dispatch('swal:error',
            title: $title,
            text: $text,
            options: $options
        );
    }

    /**
     * Dispatch warning alert
     */
    public function alertWarning($title, $text = '', $options = [])
    {
        $this->dispatch('swal:warning',
            title: $title,
            text: $text,
            options: $options
        );
    }

    /**
     * Dispatch info alert
     */
    public function alertInfo($title, $text = '', $options = [])
    {
        $this->dispatch('swal:info',
            title: $title,
            text: $text,
            options: $options
        );
    }

    /**
     * Dispatch toast notification
     */
    public function toast($message, $icon = 'success', $options = [])
    {
        $this->dispatch('swal:toast',
            message: $message,
            icon: $icon,
            options: $options
        );
    }

    /**
     * Show success toast for common operations
     */
    public function toastCreated($entity = 'Registro')
    {
        $this->toast("{$entity} creado correctamente", 'success');
    }

    public function toastUpdated($entity = 'Registro')
    {
        $this->toast("{$entity} actualizado correctamente", 'success');
    }

    public function toastDeleted($entity = 'Registro')
    {
        $this->toast("{$entity} eliminado correctamente", 'success');
    }

    public function toastActivated($entity = 'Registro')
    {
        $this->toast("{$entity} activado", 'success');
    }

    public function toastDeactivated($entity = 'Registro')
    {
        $this->toast("{$entity} desactivado correctamente", 'warning');
    }

    public function toastSuccess($text = 'Accion realizada con exito')
    {
        $this->toast("{$text}");
    }

    /**
     * Show error toast for common operations
     */
    public function toastError($message = 'Ha ocurrido un error')
    {
        $this->toast($message, 'error');
    }
}
