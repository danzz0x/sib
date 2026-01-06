<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 font-sans">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Buscar Socio</h3>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-ri-search-line class="w-5 h-5" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                            placeholder="Nombre, RNI o CI..." autofocus>
                    </div>
                    @if (count($resultados) > 0)
                        <ul
                            class="mt-4 border border-gray-200 rounded-md divide-y divide-gray-200 max-h-96 overflow-y-auto">
                            @foreach ($resultados as $socio)
                                <li>
                                    <button wire:click="seleccionarSocio({{ $socio->id }})"
                                        class="w-full text-left px-4 py-3 hover:bg-blue-50 transition flex items-center justify-between group">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 group-hover:text-blue-700">
                                                {{ $socio->nombre }}</p>
                                            <p class="text-xs text-gray-500">RNI: {{ $socio->rni }}</p>
                                        </div>
                                        <x-ri-arrow-right-s-line class="w-5 h-5" />
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(strlen($search) >= 2)
                        <div class="mt-4 text-center py-4 text-sm text-gray-500 bg-gray-50 rounded-md">No se encontraron
                            resultados.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            @if ($socioSeleccionado)
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">

                    <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $socioSeleccionado->nombre }}</h2>
                            <p class="text-slate-300 text-sm flex gap-3 mt-1">
                                <span>RNI: <strong class="text-white">{{ $socioSeleccionado->rni }}</strong></span>
                                <span class="text-slate-500">|</span>
                                <span
                                    class="text-emerald-400 font-bold">{{ $socioSeleccionado->colegio->nombre }}</span>
                            </p>
                        </div>
                        <button wire:click="cancelarSeleccion"
                            class="text-slate-400 hover:text-white transition">Cancelar</button>
                    </div>

                    <div class="p-6">
                        @error('permiso')
                            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 font-bold">
                                {{ $message }}</div>
                        @enderror

                        <div class="mb-6 bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-center gap-3">
                            <div class="bg-blue-500 p-2 rounded-full text-white"><svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <x-ri-information-2-fill class="h-4 w-4 bg-blue" />
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-blue-600 tracking-widest">Último Aporte
                                    Aprobado</p>
                                <p class="text-sm font-bold text-blue-900">{{ $ultimoPagoMes }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Concepto</label>
                                    <select wire:model.live="concepto_id" @disabled(auth()->user()->isUsuarioColegio())
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm {{ auth()->user()->isUsuarioColegio() ? 'bg-gray-100' : '' }}">
                                        <option value="">-- Seleccione --</option>
                                        @foreach ($conceptos as $c)
                                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('concepto_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($usar_periodos)
                                    <div class="bg-slate-50 p-4 rounded-md border border-slate-200">
                                        <label
                                            class="block text-xs font-bold text-slate-600 uppercase mb-2">Periodo</label>

                                        <div class="grid grid-cols-2 gap-4">

                                            {{-- SELECT PERSONALIZADO: FECHA INICIO --}}
                                            <div x-data="{ open: false, selected: @entangle('fecha_inicio') }">
                                                <span
                                                    class="text-[10px] text-gray-500 uppercase font-bold mb-1 block">Desde:</span>

                                                <div class="relative">
                                                    {{-- BOTÓN DISPARADOR (Parece un input) --}}
                                                    <button type="button" @click="open = !open"
                                                        @click.outside="open = false"
                                                        class="relative w-full cursor-default rounded-md bg-white py-2 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 sm:text-sm sm:leading-6">
                                                        <span class="block truncate">
                                                            {{ $listaMeses[$fecha_inicio] ?? 'Seleccione mes...' }}
                                                        </span>
                                                        <span
                                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </button>

                                                    {{-- MENÚ FLOTANTE CON SCROLL --}}
                                                    <ul x-show="open"
                                                        x-transition:leave="transition ease-in duration-100"
                                                        x-transition:leave-start="opacity-100"
                                                        x-transition:leave-end="opacity-0"
                                                        class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm scrollbar-thin scrollbar-thumb-gray-300">

                                                        @foreach ($listaMeses as $val => $txt)
                                                            @php
                                                                $estado = $mesesPagados[$val] ?? null;
                                                                $ocupado = !is_null($estado);
                                                            @endphp

                                                            <li @if (!$ocupado) wire:click="$set('fecha_inicio', '{{ $val }}'); open = false"
                                    @click="open = false" @endif
                                                                class="relative select-none py-2 pl-3 pr-9 {{ $ocupado ? 'text-gray-400 bg-gray-50 cursor-not-allowed italic' : 'text-gray-900 cursor-pointer hover:bg-emerald-50 hover:text-emerald-900' }}">

                                                                <div class="flex items-center justify-between">
                                                                    <span
                                                                        class="block truncate {{ $val == $fecha_inicio ? 'font-semibold' : 'font-normal' }}">
                                                                        {{ $txt }}
                                                                    </span>
                                                                    @if ($estado === 'aprobado')
                                                                        <span
                                                                            class="text-xs text-green-600 font-bold mr-2">(Pagado)</span>
                                                                    @elseif($estado === 'pendiente')
                                                                        <span
                                                                            class="text-xs text-amber-600 font-bold mr-2">(Pendiente)</span>
                                                                    @endif
                                                                </div>

                                                                @if ($val == $fecha_inicio)
                                                                    <span
                                                                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-600">
                                                                        <svg class="h-5 w-5" viewBox="0 0 20 20"
                                                                            fill="currentColor">
                                                                            <path fill-rule="evenodd"
                                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                clip-rule="evenodd" />
                                                                        </svg>
                                                                    </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- SELECT PERSONALIZADO: FECHA FIN --}}
                                            <div x-data="{ open: false }">
                                                <span
                                                    class="text-[10px] text-gray-500 uppercase font-bold mb-1 block">Hasta:</span>

                                                <div class="relative">
                                                    <button type="button" @click="open = !open"
                                                        @click.outside="open = false"
                                                        class="relative w-full cursor-default rounded-md bg-white py-2 pl-3 pr-10 text-left text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 sm:text-sm sm:leading-6">
                                                        <span class="block truncate">
                                                            {{ $listaMeses[$fecha_fin] ?? 'Seleccione mes...' }}
                                                        </span>
                                                        <span
                                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </button>

                                                    <ul x-show="open"
                                                        x-transition:leave="transition ease-in duration-100"
                                                        x-transition:leave-start="opacity-100"
                                                        x-transition:leave-end="opacity-0"
                                                        class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm scrollbar-thin scrollbar-thumb-gray-300">

                                                        @foreach ($listaMeses as $val => $txt)
                                                            @if ($val >= $fecha_inicio)
                                                                @php
                                                                    $estado = $mesesPagados[$val] ?? null;
                                                                    $ocupado = !is_null($estado);
                                                                @endphp

                                                                <li @if (!$ocupado) wire:click="$set('fecha_fin', '{{ $val }}'); open = false"
                                        @click="open = false" @endif
                                                                    class="relative select-none py-2 pl-3 pr-9 {{ $ocupado ? 'text-gray-400 bg-gray-50 cursor-not-allowed italic' : 'text-gray-900 cursor-pointer hover:bg-emerald-50 hover:text-emerald-900' }}">

                                                                    <div class="flex items-center justify-between">
                                                                        <span
                                                                            class="block truncate {{ $val == $fecha_fin ? 'font-semibold' : 'font-normal' }}">
                                                                            {{ $txt }}
                                                                        </span>
                                                                        @if ($estado === 'aprobado')
                                                                            <span
                                                                                class="text-xs text-green-600 font-bold mr-2">(Pagado)</span>
                                                                        @elseif($estado === 'pendiente')
                                                                            <span
                                                                                class="text-xs text-amber-600 font-bold mr-2">(Pendiente)</span>
                                                                        @endif
                                                                    </div>

                                                                    @if ($val == $fecha_fin)
                                                                        <span
                                                                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-600">
                                                                            <svg class="h-5 w-5" viewBox="0 0 20 20"
                                                                                fill="currentColor">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                        </span>
                                                                    @endif
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                        @error('fecha_inicio')
                                            <p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Monto Total
                                        (Bs)</label>
                                    <input type="number" disabled wire:model="monto_total"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-lg font-bold bg-emerald-50 text-emerald-700">
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-md border border-gray-200 space-y-3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Datos de Contacto</h4>
                                    @if ($pedir_telefono)
                                        <input type="text" wire:model="nuevo_telefono"
                                            placeholder="Celular (Obligatorio)"
                                            class="block w-full rounded-md border-amber-300 text-sm">
                                        @error('nuevo_telefono')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    @else
                                        <p class="text-xs text-gray-600">WhatsApp:
                                            <strong>{{ $socioSeleccionado->telefono }}</strong>
                                        </p>
                                    @endif

                                    @if ($pedir_email)
                                        <input type="email" wire:model="nuevo_email"
                                            placeholder="Correo (Opcional)"
                                            class="block w-full rounded-md border-gray-300 text-sm">
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($metodos as $m)
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="metodo_pago_id"
                                                    value="{{ $m->id }}" class="peer sr-only">
                                                <div
                                                    class="rounded-md border border-gray-300 px-3 py-2 text-xs font-bold text-gray-600 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-center transition uppercase">
                                                    {{ $m->nombre }}
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('metodo_pago_id')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nro.
                                        Comprobante</label>
                                    <input type="text" wire:model="nro_transaccion"
                                        class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                                    @error('nro_transaccion')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
                            <textarea wire:model="observacion" rows="2"
                                class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm"></textarea>
                        </div>

                        <div class="mt-8 border-t border-gray-100 pt-6 flex justify-end gap-3">
                            <button wire:click="cancelarSeleccion"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancelar</button>
                            <button wire:click="guardar" wire:loading.attr="disabled"
                                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 flex items-center gap-2 disabled:opacity-50">
                                <span wire:loading wire:target="guardar">Guardando...</span>
                                <span wire:loading.remove>Registrar Cobro</span>
                            </button>
                        </div>

                    </div>
                </div>
            @else
                <div
                    class="h-full min-h-[400px] flex items-center justify-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <div>
                        <div class="bg-gray-100 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-ri-menu-search-line class="w-10 h-10" />
                        </div>
                        <h3 class="mt-2 text-lg font-bold text-gray-900">Busca un socio para comenzar</h3>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
