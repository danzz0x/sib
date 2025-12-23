<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 font-sans">

    @if (session()->has('success'))
        <div
            class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="font-bold">{{ session('success') }}</p>
            </div>
            <button wire:click="$set('search', '')"
                class="text-green-600 hover:text-green-800 text-sm font-semibold">Cerrar</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Buscar Socio</h3>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
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
                                            <p class="text-xs text-gray-500">RNI: {{ $socio->rni }} •
                                                {{ $socio->colegio->sigla ?? 'SIB' }}</p>
                                        </div>
                                        <svg class="h-5 w-5 text-gray-300 group-hover:text-blue-500" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(strlen($search) >= 2)
                        <div class="mt-4 text-center py-4 text-sm text-gray-500 bg-gray-50 rounded-md">
                            No se encontraron resultados.
                        </div>
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
                                <span>{{ $socioSeleccionado->tipoSocio->nombre }}</span>
                                <span class="text-slate-500">|</span>
                                <span
                                    class="text-emerald-400 font-bold">{{ $socioSeleccionado->colegio->nombre }}</span>
                            </p>
                        </div>
                        <button wire:click="cancelarSeleccion" class="text-slate-400 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6">
                        @error('permiso')
                            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700 font-bold">ACCESO DENEGADO</p>
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    </div>
                                </div>
                            </div>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Concepto a
                                        Cobrar</label>
                                    <select wire:model.live="concepto_id"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                                    <div class="bg-blue-50 p-4 rounded-md border border-blue-100">
                                        <label class="block text-xs font-bold text-blue-800 uppercase mb-2">Periodo de
                                            Cobertura</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <span class="text-xs text-gray-500">Desde:</span>
                                                <select wire:model.live="fecha_inicio"
                                                    class="block w-full rounded-md border-gray-300 text-sm">
                                                    @foreach ($listaMeses as $val => $txt)
                                                        <option value="{{ $val }}">{{ $txt }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <span class="text-xs text-gray-500">Hasta:</span>
                                                <select wire:model.live="fecha_fin"
                                                    class="block w-full rounded-md border-gray-300 text-sm">
                                                    @foreach ($listaMeses as $val => $txt)
                                                        @if ($val >= $fecha_inicio)
                                                            <option value="{{ $val }}">{{ $txt }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total a Pagar
                                        (Bs)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Bs</span>
                                        </div>
                                        <input type="number" step="0.01" wire:model="monto_total"
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-lg font-bold border-gray-300 rounded-md bg-gray-50">
                                    </div>
                                    @if ($tarifa_unitaria > 0)
                                        <p class="mt-1 text-xs text-gray-500">Tarifa base calculada: Bs
                                            {{ $tarifa_unitaria }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-3 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Datos de Envío
                                    </h4>

                                    <div class="space-y-3">

                                        @if ($pedir_telefono)
                                            <div>
                                                <label class="block text-xs font-bold text-amber-700 mb-1">Registrar
                                                    Celular (Obligatorio)</label>
                                                <input type="text" wire:model="nuevo_telefono"
                                                    placeholder="Ej: 77712345"
                                                    class="block w-full rounded-md border-amber-300 focus:border-amber-500 focus:ring-amber-500 sm:text-sm bg-white">
                                                @error('nuevo_telefono')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            <div class="flex items-start gap-2">
                                                <div class="mt-0.5 text-green-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">El comprobante se enviará al
                                                        WhatsApp:</p>
                                                    <p class="text-sm font-bold text-gray-800">
                                                        {{ $socioSeleccionado->telefono }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($pedir_email)
                                            <div class="pt-2 border-t border-gray-200">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Registrar
                                                    Correo (Opcional)</label>
                                                <input type="email" wire:model="nuevo_email"
                                                    placeholder="correo@ejemplo.com"
                                                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                @error('nuevo_email')
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        @else
                                            <div class="flex items-start gap-2 pt-2 border-t border-gray-200">
                                                <div class="mt-0.5 text-green-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500">Copia al correo:</p>
                                                    <p class="text-sm font-semibold text-gray-800">
                                                        {{ $socioSeleccionado->email }}</p>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($metodos as $m)
                                            <label class="cursor-pointer">
                                                <input type="radio" wire:model="metodo_pago_id"
                                                    value="{{ $m->id }}" class="peer sr-only">
                                                <div
                                                    class="rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 text-center transition">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nro. Ref / Comprobante
                                        (Opcional)</label>
                                    <input type="text" wire:model="nro_transaccion"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Observación</label>
                                    <textarea wire:model="observacion" rows="2"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-100 pt-6 flex justify-end gap-3">
                            <button wire:click="cancelarSeleccion"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancelar
                            </button>
                            <button wire:click="guardar"
                                class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                Registrar Cobro
                            </button>
                        </div>

                    </div>
                </div>
            @else
                <div
                    class="h-full flex items-center justify-center bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-12 text-center">
                    <div>
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Busca un socio para comenzar</h3>
                        <p class="mt-1 text-sm text-gray-500">Usa el buscador de la izquierda para encontrar al socio
                            por nombre o carnet.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
