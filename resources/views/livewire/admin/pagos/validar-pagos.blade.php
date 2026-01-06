<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 font-sans">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Validación de Pagos</h1>
        <p class="text-sm text-slate-500">Pagos realizados por socios esperando aprobación.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">{{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($pagosPendientes as $pago)
            <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden hover:shadow-lg transition flex flex-col"
                wire:key="pago-{{ $pago->id }}">

                {{-- Cabecera --}}
                <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                    <span
                        class="text-xs font-bold text-slate-500 uppercase">{{ $pago->fecha_pago->diffForHumans() }}</span>
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">Pendiente</span>
                </div>

                {{-- Cuerpo --}}
                <div class="p-4 flex-1">
                    <div class="flex items-start gap-3 mb-3">
                        <div
                            class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600">
                            {{ substr($pago->socio->nombre, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $pago->socio->nombre }}</h3>
                            <p class="text-xs text-slate-500">{{ $pago->socio->colegio->slug }} •
                                {{ $pago->socio->rni }}</p>
                            <span class="text-xs text-slate-500 bg-slate-100 px-1 rounded">
                                {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('M/Y') }} -
                                {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('M/Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Concepto:</span>
                            <span class="font-medium text-slate-800">{{ $pago->concepto->nombre }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Monto:</span>
                            <span class="font-bold text-emerald-600 text-lg">{{ $pago->monto_pagado }} Bs</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ref:</span>
                            <span class="font-mono text-slate-600">{{ $pago->nro_transaccion }}</span>
                        </div>
                    </div>

                    {{-- Imagen Comprobante (Thumbnail) --}}
                    @if ($pago->comprobante_path)
                        <div class="mt-4 group relative cursor-pointer"
                            onclick="window.open('{{ Storage::url($pago->comprobante_path) }}', '_blank')">
                            <img src="{{ Storage::url($pago->comprobante_path) }}"
                                class="w-full h-32 object-cover rounded-lg border border-slate-200 group-hover:opacity-90">
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <span class="bg-black/50 text-white text-xs px-2 py-1 rounded">Ver Completo</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer Acciones --}}
                <div class="bg-slate-50 px-4 py-3 border-t border-slate-100 flex gap-2">
                    <button wire:click="confirmarRechazo({{ $pago->id }})"
                        class="flex-1 bg-white border border-red-200 text-red-600 hover:bg-red-50 py-2 rounded-lg text-sm font-medium transition">
                        Rechazar
                    </button>
                    <button wire:click="aprobar({{ $pago->id }})"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        Aprobar
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <div class="inline-flex bg-slate-100 p-4 rounded-full mb-3">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-slate-900">¡Estás al día!</h3>
                <p class="text-slate-500">No hay pagos pendientes de revisión.</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL RECHAZO --}}
    @if ($showRechazoModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/75 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 m-4">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Rechazar Pago</h3>
                <p class="text-sm text-slate-600 mb-4">Por favor indica el motivo. Esto se enviará al socio.</p>

                <textarea wire:model="motivoRechazo" rows="3"
                    class="w-full border-slate-300 rounded-lg text-sm focus:ring-red-500 focus:border-red-500"
                    placeholder="Ej: La imagen es ilegible..."></textarea>
                @error('motivoRechazo')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showRechazoModal', false)"
                        class="text-slate-500 hover:text-slate-700 font-medium text-sm">Cancelar</button>
                    <button wire:click="rechazar"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">Confirmar
                        Rechazo</button>
                </div>
            </div>
        </div>
    @endif
</div>
