<div class="relative w-full min-h-[500px] bg-gradient-to-br from-green-950 via-green-900 to-green-700 text-white">
    <div class="absolute inset-0 opacity-10 bg-cover"></div>
    <div
        class="relative z-10 container mx-auto h-full flex mt-12 flex-col md:flex-row items-center justify-center gap-8 px-4 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="flex-shrink-0">
            <div class="relative group">
                <div
                    class="absolute inset-0 rounded-3xl bg-gradient-to-tr from-green-500 via-green-700 to-green-900 blur-2xl opacity-30 group-hover:opacity-50 transition">
                </div>
                <div
                    class="relative w-32 h-32 sm:w-40 sm:h-40 md:w-48 md:h-48 lg:w-56 lg:h-56 bg-white rounded-3xl shadow-2xl flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('storage/' . $colegio->logo) }}" alt="Logo {{ $colegio->nombre }}"
                        class="w-20 h-20 sm:w-28 sm:h-28 md:w-36 md:h-36 lg:w-44 lg:h-44 object-contain transition-transform duration-500 group-hover:scale-110" />
                </div>
            </div>
        </div>
        <!-- Info -->
        <div class="flex-1 text-center md:text-left space-y-4 sm:space-y-6">
            <!-- Nombre -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                <span class="block">{{ Str::words($colegio->nombre, 2, '') }}</span>
                <span
                    class="block text-green-300">{{ Str::after($colegio->nombre, Str::words($colegio->nombre, 2, '')) }}</span>
            </h1>
            <!-- Descripción -->
            <p
                class="text-base sm:text-lg md:text-xl max-w-full sm:max-w-md md:max-w-lg lg:max-w-xl mx-auto md:mx-0 leading-relaxed opacity-90">
                {{ $colegio->descripcion }}
            </p>
            <div x-data="{ openModal: false }" x-cloak
                class="flex justify-center md:justify-start space-x-4 sm:space-x-6 pt-3 sm:pt-4">
                @foreach ($colegio->contactos as $contacto)
                    @php
                        $iconComponent = $contacto->tipo->icon();
                        $href =
                            $contacto->tipo === \App\Enums\TipoContacto::Telefono
                                ? 'tel:' . $contacto->valor
                                : $contacto->valor;
                    @endphp
                    <div class="relative">
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center space-x-2 hover:text-green-300 transition">
                            <x-dynamic-component :component="$iconComponent" class="w-5 h-5 sm:w-6 sm:h-6" />
                            @if (in_array($contacto->tipo, [\App\Enums\TipoContacto::Telefono, \App\Enums\TipoContacto::Email]))
                                <span class="hidden sm:inline">{{ $contacto->valor }}</span>
                            @endif
                        </a>
                        @auth
                            <div class="absolute -top-5 -right-5  z-[9999]">
                                <div x-data="{ open: false }">
                                    <button @click="open = !open"
                                        class="p-1 rounded-full hover:bg-white/20 bg-white flex items-center justify-center text-white/80 hover:text-white"
                                        :class="{ 'bg-white/20': open }" aria-label="Opciones del contacto"
                                        :aria-expanded="open">
                                        <x-ri-more-2-fill class="w-4 h-4 text-black" />
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute top-6 -right-2 mt-1 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1"
                                        x-transition>
                                        <button @click="openModal = true" @click = "open = false"
                                            wire:click="$dispatch('edit-contacto', { id:
                                        {{ $contacto->id }} })"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Editar
                                        </button>
                                        <button
                                            onclick="window.confirmDelete({{ $contacto->id }}, (id) => { Livewire.dispatch('delete-contacto', { id }); this.parentElement.previousElementSibling.click(); });"
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                @endforeach
                @auth
                    <button type="button" @click="openModal = true"
                        class="relative w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full shadow-lg flex items-center justify-center text-green-700 font-bold text-lg hover:bg-white hover:shadow-xl hover:scale-110 transition-all duration-300 group"
                        title="Agregar contacto">
                        <span class="transition-transform duration-300 group-hover:rotate-90">+</span>
                    </button>
                    <livewire:contactos.crud :colegio="$colegio" />
                @endauth
            </div>
        </div>
    </div>
</div>
