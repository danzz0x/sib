@props(['seccion', 'canManagePost'])

@php
    $publicaciones = $seccion->publicaciones;

    $publicaciones = $publicaciones->values();

    $carouselKey = 'carousel-' . $seccion->id . '-' . $publicaciones->count() . '-' . $seccion->updated_at;
@endphp

@if ($publicaciones->isNotEmpty())

    <div wire:key="{{ $carouselKey }}" x-data="{
        activeSlide: 0,
        slides: {{ $publicaciones->count() }},
        showFullscreen: false,
        imagenes: @js($publicaciones->map(fn($p) => $p->imagen ? Storage::url($p->imagen) : null))
    }" class="relative w-full max-w-8xl mx-auto mb-8">

        <div class="overflow-hidden rounded-2xl shadow-xl bg-gray-100 group"> {{-- Agregué group al padre --}}

            <div class="flex transition-transform duration-700 ease-in-out"
                :style="`transform: translateX(-${activeSlide * 100}%);`">

                @foreach ($publicaciones as $index => $publicacion)
                    <div class="w-full flex-shrink-0 relative">
                        @if ($publicacion->archivado)
                            <div class="absolute inset-0 border-[6px] border-red-500/50 z-20 pointer-events-none"></div>
                            <div class="absolute top-4 left-4 z-30">
                                <span
                                    class="bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg flex items-center gap-1">
                                    <x-ri-archive-line class="w-3 h-3" /> ARCHIVADO solo visible para el publicador
                                </span>
                            </div>
                        @endif

                        @if ($canManagePost)
                            <div class="absolute top-7 right-4 z-30 admin-only">
                                <x-colegio.menukebab :id="$publicacion->id" :prioridad="$publicacion->prioridad" :max="$seccion->publicaciones->max('prioridad')" />
                            </div>
                        @endif

                        @if ($publicacion->imagen)
                            <img src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                                class="w-full h-48 sm:h-64 md:h-80 lg:h-96 xl:h-[780px] object-cover cursor-pointer hover:scale-105 transition-transform duration-500 {{ $publicacion->archivado ? 'grayscale-[80%]' : '' }}"
                                @click="showFullscreen = true">
                        @else
                            <div
                                class="w-full h-48 sm:h-64 md:h-80 flex items-center justify-center bg-gray-200 text-gray-400">
                                <x-ri-image-line class="w-16 h-16 opacity-30" />
                            </div>
                        @endif

                        {{-- Información (Texto) --}}
                        @if ($publicacion->titulo || $publicacion->descripcion)
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/60 to-transparent text-white p-6 sm:p-8 md:p-10"
                                x-show="activeSlide === {{ $index }}"
                                x-transition:enter="transition ease-out duration-500 delay-200"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0">

                                @if ($publicacion->titulo)
                                    <h3 class="text-lg sm:text-2xl md:text-3xl font-bold mb-2 drop-shadow-md">
                                        {{ $publicacion->titulo }}
                                    </h3>
                                @endif

                                @if ($publicacion->descripcion)
                                    <div x-data="{ expanded: false }">
                                        <p class="text-sm md:text-base transition-all text-gray-200"
                                            :class="expanded ? '' : 'line-clamp-2'">
                                            {{ $publicacion->descripcion }}
                                        </p>
                                        @if (strlen($publicacion->descripcion) > 100)
                                            <button @click.stop="expanded = !expanded"
                                                class="text-xs font-bold text-green-400 hover:text-green-300 mt-2 focus:outline-none">
                                                <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Botón Detalles --}}
                                <div class="mt-3">
                                    <x-colegio.detalles :publicacion="$publicacion" />
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CONTROLES DE NAVEGACIÓN --}}
        @if ($publicaciones->count() > 1)
            <button @click="activeSlide = activeSlide === 0 ? slides - 1 : activeSlide - 1"
                class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-3 rounded-full transition-all hover:scale-110 backdrop-blur-sm z-40 group focus:outline-none">
                <x-ri-arrow-left-s-line class="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
            </button>

            <button @click="activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1"
                class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/30 hover:bg-black/60 text-white p-3 rounded-full transition-all hover:scale-110 backdrop-blur-sm z-40 group focus:outline-none">
                <x-ri-arrow-right-s-line class="w-6 h-6 group-hover:translate-x-1 transition-transform" />
            </button>

            {{-- Puntos Indicadores --}}
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-40">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="activeSlide = index"
                        class="h-1.5 rounded-full transition-all duration-300 shadow-sm"
                        :class="activeSlide === index ? 'bg-green-500 w-8' : 'bg-white/60 w-3 hover:bg-white'">
                    </button>
                </template>
            </div>

            {{-- Contador --}}
            <div
                class="absolute top-4 right-4 bg-black/40 backdrop-blur text-white text-xs px-2 py-1 rounded border border-white/10 z-40 font-mono">
                <span x-text="activeSlide + 1"></span> / <span x-text="slides"></span>
            </div>
        @endif

        {{-- MODAL FULLSCREEN --}}
        <div x-show="showFullscreen" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 backdrop-blur-xl"
            style="display: none;">

            <div class="relative w-full h-full flex items-center justify-center p-4"
                @click.self="showFullscreen = false">
                {{-- Imagen Fullscreen Reactiva --}}
                <img :src="imagenes[activeSlide]" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90"
                    x-transition:enter-end="opacity-100 scale-100">

                <button @click="showFullscreen = false"
                    class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors">
                    <x-ri-close-circle-line class="w-10 h-10" />
                </button>
            </div>
        </div>

    </div>
@endif
