<div x-data="{
    activeSlide: 0,
    slides: {{ count($seccion->publicaciones) }},
    showFullscreen: false,
    expandir: false,
    publicaciones: @js(
    $seccion->publicaciones->map(function ($pub) {
        return [
            'id' => $pub->id,
            'imagen' => $pub->imagen,
            'titulo' => $pub->titulo,
            'descripcion' => $pub->descripcion,
        ];
    }),
)
}" class="relative w-full max-w-8xl mx-auto">
    <div class="overflow-hidden rounded-2xl">
        <div class="flex transition-transform duration-700 ease-in-out"
            :style="`transform: translateX(-${activeSlide * 100}%);`">
            @foreach ($seccion->publicaciones as $index => $publicacion)
                <div class="w-full flex-shrink-0 relative">
                    <div
                        class="absolute left-2 top-2 z-50 bg-black/50 text-white text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-2 rounded-full">
                        {{ $publicacion->prioridad }}
                    </div>
                    <img src="{{ Storage::url($publicacion->imagen) }}" alt="Imagen de publicación"
                        class="w-full h-48 sm:h-64 md:h-80 lg:h-96 xl:h-[780px] object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                        @click="showFullscreen = true">
                    @if ($publicacion->titulo || $publicacion->descripcion)
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black
                            via-black/50 to-transparent text-white p-4 sm:p-6 md:p-8 lg:p-10"
                            x-data = "{ openDetail : false }"
                            :class="activeSlide === {{ $index }} ? 'block' : 'hidden'">
                            @if ($publicacion->titulo)
                                <h3 @click="openDetail = true"
                                    class="text-sm sm:text-xl md:text-2xl lg:text-3xl font-bold {{ !is_null($publicacion->detalles) ? ' cursor-pointer hover:text-green-800 transition' : '' }}">
                                    {{ $publicacion->titulo }}</h3>
                            @endif
                            @if ($publicacion->descripcion)
                                <button class=" text-sm font-bold" x-show="!expandir" x-on:click = "expandir = true">
                                    Ver mas...
                                </button>
                                <p x-show = "expandir" class=" text-xs mt-1 md:text-xl">{{ $publicacion->descripcion }}
                                </p>
                                <button x-show="expandir" class="text-sm font-bold" x-on:click = "expandir = false"
                                    type="">Ver
                                    menos</button>
                            @endif

                            <x-colegio.detalles :publicacion="$publicacion" />
                        </div>
                    @endif
                    @auth
                        <div :class="activeSlide === {{ $index }} ? 'block' : 'hidden'" class="z-50">
                            <x-colegio.menukebab :id="$publicacion->id" />
                        </div>
                    @endauth

                </div>
            @endforeach
        </div>
    </div>

    @if (count($seccion->publicaciones) > 1)
        <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1"
            class="absolute top-1/2 left-2 sm:left-4 md:left-5 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 sm:p-3 md:p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50 z-10"
            aria-label="Imagen anterior">
            <x-ri-arrow-left-s-line class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" />
        </button>

        <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1"
            class="absolute top-1/2 right-2 sm:right-4 md:right-5 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 sm:p-3 md:p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50 z-10"
            aria-label="Imagen siguiente">
            <x-ri-arrow-right-s-line class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" />
        </button>

        <div class="absolute bottom-2 sm:bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 sm:space-x-3">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                    class="w-2 h-2 sm:w-3 sm:h-3 md:w-3 md:h-3 rounded-full  transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                    :class="activeSlide === index ? 'bg-white shadow-lg' : 'bg-gray-500/50 hover:bg-gray-400/70'"
                    :aria-label="`Ir a imagen ${index + 1}`">
                </button>
            </template>
        </div>
    @endif
    @if (count($seccion->publicaciones) > 1)
        <div
            class="absolute top-2 sm:top-4 left-2 sm:left-4 bg-black/50 text-white text-xs sm:text-sm px-2 sm:px-3 py-1 sm:py-2 rounded-full">
            <span x-text="activeSlide + 1"></span>/<span>{{ count($seccion->publicaciones) }}</span>
        </div>
    @endif
    <div x-show="showFullscreen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
        @click.self="showFullscreen = false" @keydown.escape.window="showFullscreen = false" style="display: none;">
        <div class="relative max-w-7xl max-h-screen p-4 w-full h-full flex items-center justify-center">
            <img :src="publicaciones[activeSlide] ? '{{ Storage::url('') }}' + publicaciones[activeSlide].imagen : ''"
                :alt="publicaciones[activeSlide] ? publicaciones[activeSlide].titulo : 'Imagen de publicación'"
                class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100">

            <button @click="showFullscreen = false"
                class="absolute top-4 right-4 bg-black/50 hover:bg-black/70 text-white p-3 rounded-full transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                aria-label="Cerrar imagen">
                <x-ri-close-circle-line class="w-6 h-6" />
            </button>
            <template x-if="slides > 1">
                <div>
                    <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1"
                        class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="Imagen anterior">

                        <x-ri-arrow-left-s-line class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" />

                    </button>

                    <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1"
                        class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-2 focus:ring-white/50"
                        aria-label="Imagen siguiente">

                        <x-ri-arrow-right-s-line class="w-5 h-5 sm:w-6 sm:h-6 md:w-7 md:h-7" />

                    </button>

                    <div
                        class="absolute top-4 left-4 bg-black/50 text-white text-sm px-3 py-2 rounded-full backdrop-blur-sm">
                        <span x-text="activeSlide + 1"></span> / <span x-text="slides"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>
<style>
    @media (max-width: 640px) {
        .carousel-image {
            object-fit: contain !important;
            background: #f3f4f6;
        }
    }

    .cursor-grab,
    .cursor-grabbing {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (hover: none) and (pointer: coarse) {
        .transition-transform {
            transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
    }

    body:has([x-show="showFullscreen"]:not([style*="display: none"])) {
        overflow: hidden;
    }

    .cursor-pointer:hover {
        transform: scale(1.02);
    }
</style>
