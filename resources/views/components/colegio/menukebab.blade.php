@props(['id', 'directorio' => false, 'details' => true])

<div x-data="{ open: false }" {{ $attributes->merge(['class' => 'absolute top-4 right-4 z-20']) }}>
    <div class="relative">

        <button @click.stop="open = !open"
            class="p-1 rounded-full bg-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-gray-300 flex items-center justify-center"
            aria-label="Abrir menú de opciones" aria-expanded="open" x-ref="menuButton">
            <x-ri-more-2-fill class="w-5 h-5" />
        </button>

        <div x-show="open" @click.away="open = false"
            class="absolute right-4 top-2 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <button @click.stop="{{ $directorio ? 'openModalDirec = true' : 'openModalPub = true' }}"
                wire:click="$dispatch('edit-publicacion', { id: {{ $id }}})"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100"
                @click="open = false">
                Editar
            </button>

            <button
                onclick="window.confirmDelete({{ $id }}, (id) => Livewire.dispatch('delete-publicacion', { id: id }))"
                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50"
                @click.stop="open = false">
                Eliminar
            </button>

            @if ($details)
                <button @click.stop="openModalDetalle = !openModalDetalle"
                    wire:click="$dispatch('store-detalles', { id: {{ $id }}})"
                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 focus:outline-none focus:bg-red-50"
                    @click="open = false">
                    Detalles
                </button>
            @endif
        </div>
    </div>
</div>
