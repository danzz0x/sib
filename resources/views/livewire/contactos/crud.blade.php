<div x-init="$watch('openModal', value => { if (!value) { $dispatch('reset-form'); } })" @close-modal.window="openModal = false">
    <div x-show="openModal" x-on:click="openModal = false" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90" class="fixed inset-0 z-100 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
            <div class="relative bg-white text-black rounded-xl shadow-2xl p-6 w-full max-w-md transform transition-all"
                @click.stop>
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#213502]/10 rounded-full flex items-center justify-center">
                            <x-ri-message-2-line class="w-6 h-6" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $isEditing ? 'Editar Contacto' : 'Nuevo Contacto' }}
                        </h3>
                    </div>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <x-ri-close-circle-line class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tipo de Contacto <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="tipo"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm">
                            <option value="">Seleccionar tipo</option>
                            @foreach (\App\Enums\TipoContacto::cases() as $tipoContacto)
                                <option value="{{ $tipoContacto->value }}">
                                    {{ ucfirst($tipoContacto->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            @switch($tipo)
                                @case('email')
                                    Email <span class="text-red-500">*</span>
                                @break

                                @case('telefono')
                                    Teléfono <span class="text-red-500">*</span>
                                @break

                                @case('facebook')
                                @case('instagram')

                                @case('twitter')
                                @case('youtube')
                                    URL de {{ ucfirst($tipo) }} <span class="text-red-500">*</span>
                                @break

                                @default
                                    Valor <span class="text-red-500">*</span>
                            @endswitch
                        </label>

                        @switch($tipo)
                            @case('email')
                                <input type="email" wire:model.defer="valor"
                                    class="text-black w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                    placeholder="ejemplo@correo.com">
                            @break

                            @case('telefono')
                                <input type="tel" wire:model.defer="valor"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                    placeholder="+591 70123456">
                            @break

                            @case('facebook')
                            @case('instagram')

                            @case('tiktok')
                            @case('twitter')

                            @case('youtube')
                            @case('otro')
                                <input type="url" wire:model.defer="valor"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                    placeholder="https://www.ejemplo.com">
                            @break

                            @default
                                <input type="text" wire:model.defer="valor"
                                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-[#213502] focus:border-[#213502] text-sm"
                                    placeholder="Ingresa el valor">
                        @endswitch

                        @error('valor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
