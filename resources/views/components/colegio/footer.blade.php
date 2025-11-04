<div class="flex justify-center w-full bg-[#034a2b] px-6 py-4 md:gap-16">
    <div class="flex flex-col justify-center">
        <div class="flex items-center space-x-2">
            <img src="{{ Storage::url('logo/SIB-BLANCO.png') }}" alt="Logo S.I.B - Potosí" class="h-16 w-16 m-2">
            <h3 class="text-white text-sm font-bold">S.I.B - POTOSÍ</h3>
        </div>
        <p class="text-white text-sm font-bold pl-2 md:pl-0">
            Dirección: C. Venezuela No. 78 Esq. Calderón
        </p>
    </div>

    <!-- Separador solo entre los dos bloques -->
    <div class="w-px bg-white/30 mx-6 hidden md:block"></div>

    <!-- Bloque Colegio -->
    <div class="flex flex-col justify-center">
        <div class="flex items-center space-x-2">
            <img src="{{ Storage::url($colegio->logo) }}" alt="Logo {{ $colegio->nombre }}" class="h-16 w-16 m-2">
            <h3 class="text-white text-xs font-bold">{{ Str::limit($colegio->nombre_limpio, 50) }}</h3>
        </div>
        <div class="flex items-center flex-wrap gap-4 ml-4 mb-2 text-white font-extrabold">
            @foreach ($colegio->contactos as $contacto)
                @php
                    $iconComponent = $contacto->tipo->icon();
                    $href =
                        $contacto->tipo === \App\Enums\TipoContacto::Telefono
                            ? 'tel:' . $contacto->valor
                            : $contacto->valor;
                @endphp

                <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
                    class="flex items-center space-x-1">
                    <x-dynamic-component :component="$iconComponent" class="w-5 h-5" />
                    @if ($contacto->tipo === \App\Enums\TipoContacto::Telefono)
                        <span class="text-sm md:text-base">{{ $contacto->valor }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
