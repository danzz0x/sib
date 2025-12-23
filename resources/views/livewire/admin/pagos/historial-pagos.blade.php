<div class="p-6 font-sans">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Historial de Pagos</h1>
            <p class="text-sm text-slate-500">Auditoría completa de transacciones y aportes.</p>
        </div>

        {{-- BOTÓN EXPORTAR (Conectado) --}}
        <button wire:click="exportarPdf" wire:loading.attr="disabled"
            class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-medium shadow-sm flex items-center gap-2 disabled:opacity-50">
            <x-ri-file-pdf-2-line class="w-4 h-4 text-red-600" />
            <span>Descargar PDF</span>
            <span wire:loading wire:target="exportarPdf"
                class="ml-2 animate-spin rounded-full h-3 w-3 border-b-2 border-slate-700"></span>
        </button>
    </div>

    {{-- BARRA DE FILTROS AVANZADA --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-6">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            {{-- Buscador --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Buscador Global</label>
                <div class="relative">
                    <x-ri-search-line class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
                    <input type="text" wire:model.live.debounce.500ms="search"
                        placeholder="Nombre socio, RNI, Cédula o Nro Recibo..."
                        class="w-full pl-9 rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                </div>
            </div>

            {{-- Rango de Fechas --}}
            <div class="md:col-span-2 flex gap-2">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Desde</label>
                    <input type="date" wire:model.live="fecha_desde"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hasta</label>
                    <input type="date" wire:model.live="fecha_hasta"
                        class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 pt-4 border-t border-slate-100">

            {{-- Filtro: Colegio --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Colegio</label>
                <select wire:model.live="colegio_id"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Todos los Colegios</option>
                    @foreach ($colegios as $col)
                        <option value="{{ $col->id }}">{{ $col->sigla ?? substr($col->nombre, 11, 25) . '...' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro: Concepto --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Concepto</label>
                <select wire:model.live="concepto_id"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Todos</option>
                    @foreach ($conceptos as $con)
                        <option value="{{ $con->id }}">{{ $con->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro: Tipo Socio --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo Socio</label>
                <select wire:model.live="tipo_socio_id"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Todos</option>
                    @foreach ($tiposSocio as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro: Estado --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Estado Pago</label>
                <select wire:model.live="estado"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Cualquiera</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="rechazado">Rechazado</option>
                </select>
            </div>

            {{-- Botón Limpiar --}}
            <div class="flex items-end">
                <button wire:click="limpiarFiltros"
                    class="w-full py-2 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-lg text-sm font-medium transition flex justify-center items-center gap-1">
                    <x-ri-filter-off-line class="w-4 h-4" /> Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS (Se mantiene igual que antes) --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- ... (Tu código de tabla existente: thead, tbody, foreach...) ... --}}
        {{-- Solo asegúrate de que use $pagos como antes --}}

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Fecha / Recibo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Socio</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Detalle</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($pagos as $pago)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-slate-900">{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                                    <span class="text-xs text-slate-500">{{ $pago->fecha_pago->format('H:i') }}</span>
                                    @if ($pago->nro_transaccion)
                                        <span
                                            class="text-[10px] text-slate-400 font-mono mt-1">#{{ $pago->nro_transaccion }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900">{{ $pago->socio->nombre }}</span>
                                    <span class="text-xs text-slate-500">RNI: {{ $pago->socio->rni }}</span>
                                    <span
                                        class="text-[10px] text-indigo-600 font-bold mt-0.5">{{ $pago->socio->colegio->sigla ?? 'SIB' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-medium text-slate-700 block">{{ $pago->concepto->nombre }}</span>
                                @if ($pago->periodo_inicio)
                                    <span class="text-xs text-slate-500 bg-slate-100 px-1 rounded">
                                        {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('M/Y') }} -
                                        {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('M/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-slate-900">Bs
                                    {{ number_format($pago->monto_pagado, 2) }}</span>
                                <span class="text-xs text-slate-400 block">{{ $pago->metodoPago->nombre }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $pago->estado === 'aprobado'
                                        ? 'bg-green-100 text-green-800'
                                        : ($pago->estado === 'pendiente'
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($pago->estado) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500">No hay pagos con estos
                                filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $pagos->links() }}
        </div>
    </div>
</div>
