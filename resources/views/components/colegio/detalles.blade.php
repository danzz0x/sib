@props(['publicacion', 'openDetail'])

<template x-teleport="body">
    <div x-show="openDetail" x-on:keydown.escape.window="openDetail= false"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50" style="display: none;"
        role="dialog" aria-modal="true">>

        <!-- Contenido del Modal -->
        <div @click.away = "openDetail = false" x-on:click.stop x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[95vh] overflow-y-auto relative">

            <button x-on:click="openDetail = false"
                class=" fixed top-2 right-2 bg-white rounded-full p-2 shadow-lg hover:shadow-xl transition-shadow z-10"
                aria-label="Cerrar modal">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>


            <div class="flex flex-col md:flex-row items-center">
                @if ($publicacion->imagen)
                    <img src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                        class="w-full max-h-96 object-contain bg-green-800 rounded-lg m-4 ">
                @else
                    <div
                        class="w-full  h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-2xl flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <h3 class="text-2xl font-bold text-gray-900 m-2">
                    {{ $publicacion->titulo }}
                </h3>

            </div>

            <!-- Contenido -->
            <div class="p-6">
                <!-- Metadatos -->
                <div class="flex flex-wrap gap-4 mb-4 text-sm text-gray-600">
                    @if ($publicacion->ubicacion)
                        <div class="flex items-center">
                            <x-ri-map-pin-line class="w-4 h-4 mr-1" />
                            <span>{{ $publicacion->ubicacion }}</span>
                        </div>
                    @endif

                    @if ($publicacion->fecha_inicio)
                        <div class="flex items-center">
                            <x-ri-calendar-event-line class="w-4 h-4 mr-1" />
                            <span>{{ $publicacion->fecha_inicio->translatedFormat('d \d\e M \d\e Y') }}</span>
                        </div>
                    @endif

                </div>

                <!-- Detalles completos (HTML) -->
                @if ($publicacion->detalles)
                    <div class="prose prose-lg max-w-none prose-zinc text-xs md:text-lg mb-6 border-t pt-4">
                        {!! $publicacion->detalles_html !!}
                    </div>
                @endif

                <!-- URL externa -->
                @if ($publicacion->url)
                    <div class="border-t pt-4">
                        <a href="{{ $publicacion->url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center px-4 py-2 bg-green-800 text-white rounded-lg hover:bg-green-900 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Ver más información
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</template>
