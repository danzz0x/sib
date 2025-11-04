<div x-init="$watch('openModal', value => { if (!value) { $dispatch('reset-form'); } })" @close-modal.window="openModal = false">
    <div x-show="openModal" x-on:click="openModal=false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="relative bg-white rounded-xl shadow-2xl p-6 w-full max-w-md transform transition-all"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#213502]/10 rounded-full flex items-center justify-center">
                            <x-ri-layout-grid-line class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $isEditing ? 'Editar Sección' : 'Nueva Sección' }}
                        </h3>
                    </div>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-ri-close-circle-line class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Título de la Sección <span class="text-gray-400">(opcional)</span>
                        </label>
                        <input type="text" wire:model.defer="titulo"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                            placeholder="Ej: Noticias, Eventos, Galería (opcional)" maxlength="150">
                        <p class="mt-1 text-xs text-gray-500">
                            Si no especificas un título, se usará el tipo de visualización
                        </p>
                        @error('titulo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Visualización <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="tipo_mostrar"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm">
                            <option value="">Seleccionar tipo</option>
                            @foreach (\App\Enums\TipoMostrarSeccion::cases() as $tipoMostrar)
                                <option value="{{ $tipoMostrar->value }}">
                                    {{ $tipoMostrar->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_mostrar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Vista previa del tipo seleccionado --}}
                        @if ($tipo_mostrar)
                            <div class="mt-3 p-4 bg-gray-50 rounded-lg border-l-4 border-[#213502]">
                                <div class="flex items-center mb-2">

                                    <x-dynamic-component :component="App\Enums\TipoMostrarSeccion::from($tipo_mostrar)->icon()" class="w-4 h-4 text-[#213502] mr-2" />
                                    <p class="text-sm font-medium text-gray-700">Vista previa:</p>
                                </div>
                                @switch($tipo_mostrar)
                                    @case('carrusel')
                                        <div class="flex space-x-2 mb-2">
                                            <div
                                                class="w-12 h-8 bg-blue-200 rounded flex-shrink-0 flex items-center justify-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded"></div>
                                            </div>
                                            <div
                                                class="w-12 h-8 bg-blue-300 rounded flex-shrink-0 flex items-center justify-center">
                                                <div class="w-2 h-2 bg-blue-600 rounded"></div>
                                            </div>
                                            <div
                                                class="w-12 h-8 bg-blue-200 rounded flex-shrink-0 flex items-center justify-center">
                                                <div class="w-2 h-2 bg-blue-500 rounded"></div>
                                            </div>
                                            <x-ri-arrow-right-s-line class="w-4 h-4 text-gray-400 self-center" />
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ App\Enums\TipoMostrarSeccion::Carrusel->descripcion() }}</p>
                                    @break

                                    @case('cuadricula')
                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <div class="w-full h-6 bg-green-200 rounded flex items-center justify-center">
                                                <div class="w-2 h-2 bg-green-600 rounded"></div>
                                            </div>
                                            <div class="w-full h-6 bg-green-200 rounded flex items-center justify-center">
                                                <div class="w-2 h-2 bg-green-600 rounded"></div>
                                            </div>
                                            <div class="w-full h-6 bg-green-200 rounded flex items-center justify-center">
                                                <div class="w-2 h-2 bg-green-600 rounded"></div>
                                            </div>
                                            <div class="w-full h-6 bg-green-200 rounded flex items-center justify-center">
                                                <div class="w-2 h-2 bg-green-600 rounded"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ App\Enums\TipoMostrarSeccion::Cuadricula->descripcion() }}</p>
                                    @break

                                    @case('lista')
                                        <div class="space-y-1 mb-2">
                                            <div class="w-full h-4 bg-purple-200 rounded flex items-center px-2">
                                                <div class="w-2 h-2 bg-purple-600 rounded mr-2"></div>
                                                <div class="w-16 h-1 bg-purple-400 rounded"></div>
                                            </div>
                                            <div class="w-full h-4 bg-purple-200 rounded flex items-center px-2">
                                                <div class="w-2 h-2 bg-purple-600 rounded mr-2"></div>
                                                <div class="w-20 h-1 bg-purple-400 rounded"></div>
                                            </div>
                                            <div class="w-full h-4 bg-purple-200 rounded flex items-center px-2">
                                                <div class="w-2 h-2 bg-purple-600 rounded mr-2"></div>
                                                <div class="w-14 h-1 bg-purple-400 rounded"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ App\Enums\TipoMostrarSeccion::Lista->descripcion() }}</p>
                                    @break

                                    @case('directorio')
                                        <div class="space-y-1 mb-2">
                                            <div class="w-full h-4 bg-yellow-200 rounded flex items-center px-2">
                                                <div class="w-6 h-6 bg-yellow-400 rounded-full mr-2"></div>
                                                <div class="w-16 h-1 bg-yellow-400 rounded"></div>
                                            </div>
                                            <div class="w-full h-4 bg-yellow-200 rounded flex items-center px-2">
                                                <div class="w-6 h-6 bg-yellow-400 rounded-full mr-2"></div>
                                                <div class="w-20 h-1 bg-yellow-400 rounded"></div>
                                            </div>
                                            <div class="w-full h-4 bg-yellow-200 rounded flex items-center px-2">
                                                <div class="w-6 h-6 bg-yellow-400 rounded-full mr-2"></div>
                                                <div class="w-14 h-1 bg-yellow-400 rounded"></div>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-600">
                                            {{ App\Enums\TipoMostrarSeccion::Directorio->descripcion() }}</p>
                                    @break
                                @endswitch
                            </div>
                        @endif
                    </div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" @click="openModal = false"
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
