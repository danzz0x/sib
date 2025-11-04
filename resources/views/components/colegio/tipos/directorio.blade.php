<div class="w-full py-6">
    <div class="flex flex-wrap justify-center gap-6 max-w-7xl mx-auto">
        @forelse($seccion->publicaciones as $publicacion)
            <div class="relative flex items-center w-full sm:w-80 md:w-[28rem] lg:w-[32rem] px-4">
                @auth <x-colegio.menukebab :id="$publicacion->id" :directorio="true" :details="false" /> @endauth
                @if ($publicacion->imagen)
                    <img src="{{ Storage::url($publicacion->imagen) }}" alt="{{ $publicacion->titulo }}"
                        class="h-16 w-16 object-cover rounded-full border-2 border-r-green-900 flex-shrink-0">
                @endif
                <div class="flex flex-col ml-4 truncate">
                    <h1 class="text-base md:text-lg font-semibold text-gray-800 truncate">
                        {{ $publicacion->titulo }}
                    </h1>
                    @if ($publicacion->descripcion)
                        <p class="text-sm text-gray-600 truncate">
                            {{ $publicacion->descripcion }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
        @endforelse
    </div>
</div>
