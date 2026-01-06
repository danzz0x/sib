@props([
    'action',
    'id',
    'active' => false,
    'confirmMessage' => '¿Estás seguro?',
    'titleActive' => 'Desactivar',
    'titleInactive' => 'Activar',
    // DEFINIMOS ICONOS POR DEFECTO (Genéricos, por si no pasas nada)
    'iconActive' => 'ri-close-circle-line', // Icono para desactivar (Rojo)
    'iconInactive' => 'ri-checkbox-circle-line', // Icono para activar (Verde)
])

<button type="button"
    x-on:click="window.confirmStatus(
        '{{ $confirmMessage }}',
        {{ $id }},
        function(id) { $wire.{{ $action }}(id) }
    )"
    {{ $attributes->merge([
        'class' =>
            'transition-colors p-1 rounded hover:bg-opacity-50 ' .
            ($active ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50'),
    ]) }}
    title="{{ $active ? $titleActive : $titleInactive }}">
    {{-- LA MAGIA: Renderizado Dinámico de Iconos --}}
    @if ($active)
        {{-- Estado Activo (Mostramos icono para desactivar) --}}
        <x-dynamic-component :component="$iconActive" class="w-5 h-5" />
    @else
        {{-- Estado Inactivo (Mostramos icono para activar) --}}
        <x-dynamic-component :component="$iconInactive" class="w-5 h-5" />
    @endif
</button>
