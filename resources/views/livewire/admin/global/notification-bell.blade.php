<div class="relative inline-flex items-center">
    {{-- Enlace a la ruta de Validar Pagos (Asegúrate de tener la ruta con nombre) --}}
    <a href="{{ route('admin.validar') }}"
        class="relative p-2 text-slate-400 hover:text-slate-600 transition-colors duration-200 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
        title="Validar Pagos Pendientes">

        {{-- Icono Campana --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
            </path>
        </svg>

        {{-- Badge Contador (Solo si hay pagos) --}}
        @if ($count > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span
                    class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-[10px] font-bold items-center justify-center">
                    {{ $count > 9 ? '9+' : $count }}
                </span>
            </span>
        @endif
    </a>
</div>
