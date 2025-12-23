@props(['seccion', 'canManagePost'])

<div class="relative max-w-6xl mx-auto w-full  bg-white py-8" x-data="{
    currentindex: 0,
    get maxIndex() {
        return {{ $seccion->publicaciones->count() }} - 1;
    }
}">
    @if ($seccion->publicaciones->count() > 3)
        {{-- Botón anterior --}}
        <div class="absolute md:hidden -left-1 top-1/2 -translate-y-1/2 z-20">
            <button
                @click="
                currentindex = Math.max(0, currentindex - 1);
                const carousel = $refs.carousel;
                const itemWidth = carousel.children[0].offsetWidth + 16;
                carousel.scrollTo({
                    left: currentindex * itemWidth,
                    behavior: 'smooth'
                });
            "
                :disabled="currentindex === 0"
                class="p-2 rounded-full bg-white/90 hover:bg-white shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                aria-label="anterior">
                <x-ri-arrow-left-s-line class="w-6 h-6" />
            </button>
        </div>

        {{-- Botón siguiente --}}
        <div class="absolute md:hidden -right-1 top-1/2 -translate-y-1/2 z-20">
            <button
                @click="
                currentindex = Math.min(currentindex + 1, maxIndex);
                const carousel = $refs.carousel;
                const itemWidth = carousel.children[0].offsetWidth + 16;
                carousel.scrollTo({
                    left: currentindex * itemWidth,
                    behavior: 'smooth'
                });
            "
                :disabled="currentindex >= maxIndex"
                class="p-2 rounded-full bg-white/90 hover:bg-white shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                aria-label="siguiente">
                <x-ri-arrow-right-s-line class="w-6 h-6" />
            </button>
        </div>
    @endif

    <div class="px-4 md:px-0 overflow-visible">
        @if ($seccion->publicaciones->count() > 3)
            <div class="md:hidden flex gap-4 overflow-x-auto snap-x snap-mandatory scrollbar-hide -mx-4 p-4"
                x-ref="carousel"
                @scroll.debounce.150ms="
                    const scrollLeft = $el.scrollLeft;
                    const itemWidth = $el.children[0].offsetWidth + 16;
                    currentindex = Math.round(scrollLeft / itemWidth);
                ">
                @foreach ($seccion->publicaciones as $publicacion)
                    <div wire:key="pub-mobile-{{ $publicacion->id }}" x-data="{ openDetail: false }"
                        class="{{ $publicacion->css_estado }} w-[85vw] max-w-sm flex-shrink-0 snap-center cursor-pointer transition-all"
                        @if ($publicacion->imagen) @click="openDetail = true" @endif>
                        <div
                            class="group relative bg-white rounded-xl shadow-lg  transition flex flex-col overflow-hidden h-full">
                            @if ($canManagePost)
                                <x-colegio.menukebab class="admin-only" :id="$publicacion->id" />
                            @endif

                            @if ($publicacion->imagen)
                                <div class="flex items-center justify-center bg-gray-200">
                                    <img src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                                        class="object-cover h-56 w-full">
                                </div>
                            @endif

                            <div class="p-4 flex flex-col flex-1">
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @if ($publicacion->ubicacion)
                                        <div class="flex items-center">
                                            <x-ri-map-pin-line class="w-4 h-4 text-gray-500 mr-1" />
                                            <span class="text-sm text-gray-500">{{ $publicacion->ubicacion }}</span>
                                        </div>
                                    @endif

                                    @if ($publicacion->fecha_inicio)
                                        <div class="flex items-center ml-auto">
                                            <x-ri-calendar-event-line class="w-5 h-5 text-gray-500 mr-1" />
                                            <span class="text-sm text-gray-500">
                                                {{ $publicacion->fecha_inicio->translatedFormat('d \d\e M \d\e Y') }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="mb-2">
                                        <x-colegio.badge-estado :archivado="$publicacion->archivado" />
                                    </div>
                                </div>
                                <h3
                                    class="font-semibold text-lg transition-all text-gray-800 mb-2
    {{ !is_null($publicacion->detalles) || !is_null($publicacion->url) ? ' group-hover:text-green-800 ' : '' }}

    {{ $publicacion->imagen ? '' : ' text-center ' }}">
                                    {{ $publicacion->titulo }}
                                </h3>

                                <p class="text-sm text-gray-600  flex-1">
                                    {{ is_null($publicacion->imagen ? Str::limit($publicacion->descripcion, 100) : $publicacion->descripcion) }}
                                </p>

                                <x-colegio.detalles :publicacion="$publicacion" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Grid para desktop o si hay 3 o menos publicaciones --}}
        <div
            class="{{ $seccion->publicaciones->count() > 3 ? 'hidden md:flex' : 'flex' }} flex-wrap justify-center gap-6 ">
            @foreach ($seccion->publicaciones as $publicacion)
                @php
                    $claseArchivado = $publicacion->archivado ? 'admin-only' : '';
                @endphp
                <div wire:key="pub-desktop-{{ $publicacion->id }}" x-data="{ openDetail: false }"
                    class="{{ $claseArchivado }} w-full md:w-[calc(50%-12px)] cursor-pointer lg:w-[calc(33.333%-16px)] transition-all"
                    @if ($publicacion->imagen) @click="openDetail = true" @endif>
                    <div
                        class="group relative bg-white rounded-xl shadow-lg hover:shadow-2xl transition flex flex-col overflow-visible h-full">

                        @if ($canManagePost)
                            <x-colegio.menukebab class="admin-only" :id="$publicacion->id" :prioridad="$publicacion->prioridad"
                                :max="$seccion->publicaciones->max('prioridad')" />
                        @endif

                        @if ($publicacion->imagen)
                            <div class="flex items-center justify-center bg-gray-200 rounded-t-xl overflow-hidden">
                                <img src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                                    class="object-cover h-56 w-full">
                            </div>
                        @endif

                        <div class="p-4 flex flex-col flex-1">
                            <div class="flex flex-wrap gap-2 mb-2">
                                @if ($publicacion->ubicacion)
                                    <div class="flex items-center">
                                        <x-ri-map-pin-line class="w-4 h-4 text-gray-500 mr-1" />
                                        <span class="text-sm text-gray-500">{{ $publicacion->ubicacion }}</span>
                                    </div>
                                @endif

                                @if ($publicacion->fecha_inicio)
                                    <div class="flex items-center ml-auto">
                                        <x-ri-calendar-event-line class="w-5 h-5 text-gray-500 mr-1" />
                                        <span class="text-sm text-gray-500">
                                            {{ $publicacion->fecha_inicio->translatedFormat('d \d\e M \d\e Y') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-2">
                                    <x-colegio.badge-estado :archivado="$publicacion->archivado" />
                                </div>
                            </div>
                            <h3
                                class="font-semibold text-lg transition-all text-gray-800 mb-2
    {{ !is_null($publicacion->detalles) || !is_null($publicacion->url) ? ' group-hover:text-green-800 ' : '' }}

    {{ $publicacion->imagen ? '' : ' text-center ' }}">
                                {{ $publicacion->titulo }}
                            </h3>
                            <p class="text-sm {{ $publicacion->imagen ? '' : ' text-center ' }} text-gray-600 flex-1">
                                {{ $publicacion->imagen ? Str::limit($publicacion->descripcion, 100) : $publicacion->descripcion }}
                            </p>

                            <x-colegio.detalles :publicacion="$publicacion" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Indicadores --}}
    @if ($seccion->publicaciones->count() > 3)
        <div class="flex md:hidden justify-center gap-2 mt-4">
            @foreach ($seccion->publicaciones as $publicacion)
                <button wire:key="indicator-{{ $publicacion->id }}"
                    @click="
                        currentindex = {{ $loop->index }};
                        const carousel = $refs.carousel;
                        const itemWidth = carousel.children[0].offsetWidth + 16;
                        carousel.scrollTo({
                            left: {{ $loop->index }} * itemWidth,
                            behavior: 'smooth'
                        });
                    "
                    :class="currentindex === {{ $loop->index }} ? 'bg-gray-800 w-8' : 'bg-gray-300 w-2'"
                    class="h-2 rounded-full transition-all duration-300">
                </button>
            @endforeach
        </div>
    @endif

    {{-- Paginación desktop --}}
    <div wire:key="pagination-{{ $seccion->id }}" class="hidden md:flex md:justify-center mt-6">
        {{ $seccion->publicaciones->links('vendor.pagination.tailwind', ['scrollTo' => false]) }}
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
