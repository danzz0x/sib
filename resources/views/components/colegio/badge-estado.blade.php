@props(['archivado' => false])

@if ($archivado)
    <span
        {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200']) }}>
        Archivado
    </span>
@endif
