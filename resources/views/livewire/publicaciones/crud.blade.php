<div x-init="$watch('openModalPub', value => { if (!value) { $dispatch('reset-form'); } })" @close-modal.window="openModalPub = false" x-cloak>
    <div x-show="openModalPub" x-on:click="openModalPub = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="relative bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl transform transition-all"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#213502]/10 rounded-full flex items-center justify-center">
                            <x-ri-article-line class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $isEditing ? 'Editar Publicación' : 'Nueva Publicación' }}
                            </h3>
                        </div>
                    </div>
                    <button @click="openModalPub = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-ri-close-circle-line class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Título --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Título <span class="text-gray-400">(opcional)</span>
                            </label>
                            <input type="text" wire:model.defer="titulo"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                placeholder="Título de la publicación" maxlength="200">
                            @error('titulo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="flex items-center space-x-4 pt-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.defer="archivado"
                                    class="rounded border-gray-300 text-[#213502] focus:ring-[#213502]">
                                <span class="ml-2 text-sm text-gray-700">Archivar publicación</span>
                            </label>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción
                        </label>
                        <textarea wire:model.defer="descripcion"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                            rows="3" placeholder="Descripción detallada de la publicación"></textarea>
                        @error('descripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Imagen --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Imagen
                        </label>

                        @if ($imagen_actual)
                            <div class="mb-3 relative inline-block">
                                <img src="{{ asset('storage/' . $imagen_actual) }}"
                                    class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                <button type="button" wire:click="removeImage"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                    title="Eliminar imagen">
                                    <x-ri-close-line class="w-4 h-4" />
                                </button>
                            </div>
                        @endif

                        <input type="file" wire:model="imagen"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                            accept="image/*">

                        @if ($imagen)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600">Vista previa:</p>
                                <img src="{{ $imagen->temporaryUrl() }}"
                                    class="w-32 h-32 object-cover rounded-lg inline-block border border-gray-200 mt-1">
                            </div>
                        @endif

                        <p class="mt-1 text-xs text-gray-500">Formatos: JPG, PNG, GIF. Máximo 2MB</p>
                        @error('imagen')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- URL --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                URL / Enlace externo
                            </label>
                            <input type="url" wire:model.defer="url"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                placeholder="https://ejemplo.com">
                            @error('url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Ubicación --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Ubicación
                            </label>
                            <input type="text" wire:model.defer="ubicacion"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                placeholder="Ej: Auditorio Principal, Aula 101">
                            @error('ubicacion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="flex justify-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                            </label>
                            <input type="date" wire:model.defer="fecha_inicio"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm">
                            <p class="mt-1 text-xs text-gray-500">Si el evento tiene fecha</p>
                            @error('fecha_inicio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>

                    {{-- Vista previa del estado --}}
                    <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-[#213502]">
                        <h4 class="font-medium text-gray-900 mb-2">Estado de la publicación:</h4>
                        <div class="flex items-center space-x-4">
                            @if ($archivado)
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                    <x-ri-archive-line class="w-4 h-4 inline mr-1" />
                                    Archivada
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    <x-ri-eye-line class="w-4 h-4 inline mr-1" />
                                    Visible
                                </span>
                            @endif

                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                Prioridad: {{ $prioridad }}
                            </span>
                        </div>
                    </div>

                    <div class="flex space-x-3 pt-6">
                        <button type="button" @click="openModalPub = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#213502] text-white rounded-lg hover:bg-[#2d4a03] font-medium transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                {{ $isEditing ? 'Actualizar' : 'Crear' }}
                            </span>
                            <span wire:loading class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
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
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div x-init="$watch('openModalDetalle', value => { if (!value) { $dispatch('reset-form'); } })" @close-modal-detalle.window="openModalDetalle = false">
        <div x-show="openModalDetalle" x-on:click="openModalDetalle = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl">
                    <div class="bg-white rounded-lg shadow-xl overflow-hidden" @click.stop>
                        {{-- Header --}}
                        <div class="bg-gray-50 px-4 py-3 border-b">
                            hola
                        </div>

                        {{-- Body --}}
                        <div class="p-6">
                            <form wire:submit.prevent="storeDetalles">
                                <div class="mb-4">
                                    <label for="detalles" class="block text-sm font-medium text-gray-700 mb-2">
                                        Detalles en Markdown (opcional)
                                    </label>
                                    <textarea id="detalles" wire:model.defer="detalles" rows="12"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-vertical"
                                        placeholder="Escribe aquí los detalles del evento en formato Markdown. Ejemplo: # Título del Evento&#10;&#10;- Lista de items&#10;- Otro item&#10;&#10;**Texto en negrita**&#10;&#10;[Enlace externo](https://ejemplo.com)"></textarea>
                                    @error('detalles')
                                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Preview --}}


                                {{-- Mensaje de éxito --}}
                                @if (session()->has('message'))
                                    <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                                        {{ session('message') }}
                                    </div>
                                @endif

                                <div class="bg-gray-50 px-4 py-3 flex justify-end space-x-2 border-t">
                                    <button type="button" @click="openModalDetalle = false"
                                        class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="flex-1 px-4 py-2 bg-[#213502] text-white rounded-lg hover:bg-[#2d4a03] font-medium transition-colors disabled:opacity-50"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>
                                            Guardar detalles
                                        </span>
                                        <span wire:loading class="flex items-center justify-center">
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div x-init="$watch('openModalDirec', value => { if (!value) { $dispatch('reset-form'); } })" @close-modal.window="openModalDirec = false">
        <div x-show="openModalDirec" x-on:click="openModalDirec = false"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
                <div class="relative bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl transform transition-all"
                    @click.stop>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-[#213502]/10 rounded-full flex items-center justify-center">
                                <x-ri-article-line class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $isEditing ? 'Editar Publicación' : 'Nueva Publicación' }} </h3>
                            </div>
                        </div> <button @click="openModalDirec = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                            <x-ri-close-circle-line class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit.prevent="store" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre
                                </label>
                                <input type="text" wire:model.defer="titulo"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                    placeholder="Nombre completo" maxlength="200">
                                @error('titulo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>



                            {{-- Estado --}}
                            <div class="flex items-center space-x-4 pt-6">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.defer="archivado"
                                        class="rounded border-gray-300 text-[#213502] focus:ring-[#213502]">
                                    <span class="ml-2 text-sm text-gray-700">Archivar publicación</span>
                                </label>
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cargo
                            </label>
                            <textarea wire:model.defer="descripcion"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                rows="3" placeholder="Descripción detallada de la publicación"></textarea>
                            @error('descripcion')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Imagen --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Imagen
                            </label>

                            @if ($imagen_actual)
                                <div class="mb-3 relative inline-block">
                                    <img src="{{ asset('storage/' . $imagen_actual) }}"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-200">
                                    <button type="button" wire:click="removeImage"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors"
                                        title="Eliminar imagen">
                                        <x-ri-close-line class="w-4 h-4" />
                                    </button>
                                </div>
                            @endif

                            <input type="file" wire:model="imagen"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                accept="image/*">

                            @if ($imagen)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Vista previa:</p>
                                    <img src="{{ $imagen->temporaryUrl() }}"
                                        class="w-32 h-32 object-cover rounded-lg border border-gray-200 mt-1">
                                </div>
                            @endif

                            <p class="mt-1 text-xs text-gray-500">Formatos: JPG, PNG, GIF. Máximo 2MB</p>
                            @error('imagen')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Vista previa del estado --}}
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-[#213502]">
                            <h4 class="font-medium text-gray-900 mb-2">Estado de la publicación:</h4>
                            <div class="flex items-center space-x-4">
                                @if ($archivado)
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                        <x-ri-archive-line class="w-4 h-4 inline mr-1" />
                                        Archivada
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                        <x-ri-eye-line class="w-4 h-4 inline mr-1" />
                                        Visible
                                    </span>
                                @endif

                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    Prioridad: {{ $prioridad }}
                                </span>
                            </div>
                        </div>

                        <div class="flex space-x-3 pt-6">
                            <button type="button" @click="openModalDirec = false"
                                class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2 bg-[#213502] text-white rounded-lg hover:bg-[#2d4a03] font-medium transition-colors disabled:opacity-50"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    {{ $isEditing ? 'Actualizar' : 'Crear' }}
                                </span>
                                <span wire:loading class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none"
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
