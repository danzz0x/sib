<div class="w-full py-6">

    <div class="flex flex-wrap justify-center gap-6 max-w-7xl mx-auto">
        @foreach ($seccion->publicaciones as $publicacion)
            <div x-data="{ showFullscreen: false }" class="relative flex items-center w-full sm:w-80 md:w-[28rem] lg:w-[32rem] px-4">
                @auth <x-colegio.menukebab :id="$publicacion->id" :directorio="true" :details="false" :prioridad="$publicacion->prioridad"
                        :max="$seccion->publicaciones->max('prioridad')" />
                @endauth
                @if ($publicacion->imagen)
                    <img @click="showFullscreen = true" src="{{ Storage::url($publicacion->imagen) }}"
                        alt="{{ $publicacion->titulo }}"
                        class="h-16 w-16 object-cover rounded-full border-2 border-r-green-900 flex-shrink-0 cursor-pointer">
                @endif
                <div class="flex flex-col ml-4 truncate">
                    <h1 class="text-sm md:text-lg font-semibold text-gray-800 truncate">
                        {{ $publicacion->titulo }}
                    </h1>
                    @if ($publicacion->descripcion)
                        <p class="text-sm text-gray-600 truncate">
                            {{ $publicacion->descripcion }}
                        </p>
                    @endif
                </div>
                <div x-show="showFullscreen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="fixed inset-0 z-50 flex items-center justify-center " @click.self="showFullscreen = false"
                    @keydown.escape.window="showFullscreen = false" style="display: none;">
                    <div @click.outside="showFullscreen = false"
                        class="relative max-w-7xl bg-none max-h-screen p-4 w-full h-full flex items-center justify-center">
                        <img src="{{ Storage::url($publicacion->imagen) }}"
                            class=" w-full h-1/2 object-contain rounded-lg">

                        <button @click="showFullscreen = false"
                            class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                            aria-label="Cerrar imagen">
                            <x-ri-close-circle-line class="w-6 h-6" />
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-center space-x-4 mb-6">
        @foreach ($aniosDisponibles as $anio)
            @php $isActive = $anio == $anioSeleccionado; @endphp
            <button wire:click="cambiarAnio({{ $anio }})"
                class="relative py-2 px-4 text-sm font-medium transition-all duration-200
                {{ $isActive ? 'text-[#213502] font-semibold' : 'text-gray-600 hover:text-[#213502]' }}">
                {{ $anio }}
                @if ($isActive)
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-[#213502] rounded-full"></span>
                @endif
            </button>
        @endforeach
    </div>
</div>
