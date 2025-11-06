@php
    $items = $seccion->publicaciones->count();

    $sm_clase = $items > 3 ? 'h-[300px]' : 'auto';
    $md_clase = $items > 3 ? 'md:h-[545px]' : 'auto';
@endphp
<div class="w-full overflow-visible">
    <div class="max-w-7xl mx-auto pr-4 md:px-4  sm:px-6">
        <div class="relative overflow-y-auto scrollbar-thin p-4 {{ $sm_clase }} {{ $md_clase }}">
            @foreach ($seccion->publicaciones as $publicacion)
                <div x-data = "{ openDetail : false }" @click = "openDetail = true"
                    class="relative group shadow-lg mb-6 flex flex-row items-center cursor-pointer transition-all overflow-hidden w-full snap-start">
                    @auth
                        <x-colegio.menukebab :id="$publicacion->id" :prioridad="$publicacion->prioridad" :max="$seccion->publicaciones->max('prioridad')"
                            class="absolute top-2 right-2 z-10" />
                    @endauth

                    @if ($publicacion->imagen)
                        <div class="flex-shrink-0 w-1/3 sm:w-1/4 md:w-1/5 h-32 sm:h-56 md:h-64 bg-gray-200"> <img
                                src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                                class="object-cover w-full h-full">
                        </div>
                    @endif

                    <div class="p-4 flex flex-col justify-between w-2/3  sm:w-3/4 md:w-4/5">
                        <div>
                            <h3
                                class="font-bold text-xs md:text-xl   transition-all
                                {{ !is_null($publicacion->detalles) ? ' group-hover:text-green-800 ' : '' }}
                                ">
                                {{ $publicacion->titulo }}</h3>

                            @if ($publicacion->fecha_inicio)
                                <p class="text-xs sm:text-base mt-1">
                                    {{ $publicacion->fecha_inicio->translatedFormat('d \d\e M \d\e Y') }}</p>
                            @endif
                        </div>
                        <div class="hidden md:flex mt-2 text-sm sm:text-base">
                            <p>{{ Str::limit($publicacion->descripcion, 150) }}</p>
                        </div>
                    </div>


                    <x-colegio.detalles :publicacion="$publicacion" />
                </div>
            @endforeach
        </div>

    </div>
    <style>
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #21801D #f3f4f6;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</div>
