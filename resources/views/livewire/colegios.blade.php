<div x-data="{ openModal: false }" x-init="$watch('openModal', value => {
    if (!value) {
        $wire.call('resetFields');
    }
})" class="p-6">
    <header class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestión de Colegios</h1>
        <button @click="openModal = true" aria-label="Crear nuevo colegio"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md">
            <x-ri-add-line class="w-4 h-4 inline-block" />
            Nuevo Colegio
        </button>
    </header>

    <article class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 items-stretch">
        @forelse($colegios as $colegio)
            <div
                class="relative bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 p-6 border border-gray-100 flex flex-col h-full">

                <div class="absolute top-3 right-3">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="p-1 rounded-full hover:bg-gray-100 flex items-center justify-center"
                            :class="{ 'bg-gray-100': open }" aria-label="Opciones del colegio" :aria-expanded="open">
                            <x-ri-more-2-fill class="w-5 h-5" />
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            class="absolute top-5 right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1"
                            x-transition>
                            <button @click="openModal = !openModal; open = false" wire:click="edit({{ $colegio->id }})"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Editar
                            </button>
                            <button
                                onclick="window.confirmDelete({{ $colegio->id }}, (id) => @this.call('delete', id)); this.parentElement.previousElementSibling.click()"
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4 mb-4">
                    @if ($colegio->logo)
                        <img src="{{ Storage::url($colegio->logo) }}" alt="{{ $colegio->nombre }}"
                            class="w-12 h-12 rounded-full object-cover flex-shrink-0 ring-2 ring-gray-100"
                            loading="lazy" decoding="async">
                    @else
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                            {{ substr($colegio->nombre, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900" title="{{ $colegio->nombre }}">
                            {{ $colegio->nombre }}
                        </h3>
                        @if ($colegio->descripcion)
                            <p class="text-gray-500 text-sm line-clamp-2 mt-1" title="{{ $colegio->descripcion }}">
                                {{ $colegio->descripcion }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex mb-4 mt-auto">
                    <button wire:click="activar({{ $colegio->id }})"
                        class="w-full text-center transition-colors duration-200 disabled:opacity-50"
                        wire:loading.attr="disabled" wire:target="activar({{ $colegio->id }})">
                        @if ($colegio->activo)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span wire:loading.remove wire:target="activar({{ $colegio->id }})">Activo</span>
                                <span wire:loading wire:target="activar({{ $colegio->id }})">Cambiando...</span>
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span wire:loading.remove wire:target="activar({{ $colegio->id }})">Inactivo</span>
                                <span wire:loading wire:target="activar({{ $colegio->id }})">Cambiando...</span>
                            </span>
                        @endif
                    </button>
                </div>

                <div class="flex space-x-2">
                    <a href="{{ route('colegios.show', $colegio->slug) }}"
                        class="flex-1 text-center bg-gradient-to-r from-green-600 to-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium shadow-sm hover:from-green-700 hover:to-green-800 transition-colors duration-200">
                        Entrar
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay colegios</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer colegio.</p>
                    <div class="mt-6">
                        <button @click="openModal = true"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <x-ri-more-2-fill class="w-5 h-5" />
                            Nuevo Colegio
                        </button>
                    </div>
                </div>
            </div>
        @endforelse
    </article>

    <div x-show="openModal" @click.away="openModal = false" @close-modal.window="openModal=false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"
                @click="openModal = false"></div>

            <!-- Modal Content -->
            <div
                class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="openModal = false"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none">
                        <span class="sr-only">Cerrar</span>
                        <x-ri-close-circle-line class="w-6 h-6" />
                    </button>
                </div>

                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            {{ $colegioId ? 'Editar Colegio' : 'Crear Colegio' }}
                        </h3>

                        <form wire:submit.prevent="store" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live.debounce.500ms="nombre"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    placeholder="Ej. Colegio de Ingenieros Civiles (CIC-POTOSÍ) " maxlength="255"
                                    required>
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea wire:model.defer="descripcion"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    rows="3" maxlength="500" placeholder="Descripción del colegio"></textarea>
                                @error('descripcion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                                <div
                                    class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                                    <div class="space-y-1 text-center">
                                        @if ($nuevoLogo)
                                            <div class="relative">
                                                <img src="{{ $nuevoLogo->temporaryUrl() }}"
                                                    class="mx-auto h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                                                <button type="button" wire:click="$set('nuevoLogo', null)"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                                    ×
                                                </button>
                                            </div>
                                        @elseif ($logoPreview)
                                            <img src="{{ Storage::url($logoPreview) }}"
                                                class="mx-auto h-20 w-20 rounded-full object-cover border-2 border-gray-200">
                                        @else
                                            <x-ri-image-add-fill class="mx-auto h-12 w-12 text-gray-400" />
                                        @endif
                                        <div class="flex text-sm text-gray-600">
                                            <label
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none">
                                                <span>Subir archivo</span>
                                                <input wire:model="nuevoLogo" type="file"
                                                    accept="image/jpeg,image/png,image/webp" class="sr-only"
                                                    onchange="validateFile(this)">
                                            </label>
                                            <p class="pl-1">o arrastra y suelta</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, WebP hasta 2MB</p>
                                        <div wire:loading wire:target="nuevoLogo" class="text-sm text-green-600">
                                            Subiendo imagen...
                                        </div>
                                    </div>
                                </div>
                                @error('nuevoLogo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled" wire:target="store">
                                    <span wire:loading.remove wire:target="store">
                                        {{ $colegioId ? 'Actualizar' : 'Crear' }}
                                    </span>
                                    <span wire:loading wire:target="store" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Guardando...
                                    </span>
                                </button>

                                <button type="button" @click="openModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function validateFile(input) {
        const file = input.files[0];
        if (file) {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (file.size > maxSize) {
                alert('El archivo es demasiado grande. Máximo 2MB.');
                input.value = '';
                return;
            }

            if (!allowedTypes.includes(file.type)) {
                alert('Tipo de archivo no válido. Solo se permiten JPG, PNG y WebP.');
                input.value = '';
                return;
            }
        }
    }
</script>
