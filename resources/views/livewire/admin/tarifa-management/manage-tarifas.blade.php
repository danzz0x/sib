<div x-data="{ openModal: @entangle('openModal') }" x-init="$watch('openModal', value => { if (!value) { $wire.call('resetFields'); } })" class="p-6 font-sans">

    {{-- ENCABEZADO --}}
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Tarifas</h1>
            <p class="text-sm text-gray-500 mt-1">Configura los precios de aportes y trámites.</p>
        </div>
        <button @click="openModal = true" wire:click="create"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md flex items-center gap-2">
            <x-ri-money-dollar-circle-line class="w-4 h-4" />
            Nueva Tarifa
        </button>
    </header>

    {{-- FEEDBACK --}}
    @if (session()->has('success'))
        <div
            class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-center justify-between">
            <span class="font-medium text-sm">{{ session('success') }}</span>
            <x-ri-close-line class="w-5 h-5 cursor-pointer" onclick="this.parentElement.remove()" />
        </div>
    @endif

    {{-- BÚSQUEDA --}}
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por Concepto o Colegio..."
            class="w-full sm:w-1/3 rounded-lg border-gray-300 focus:ring-green-500 focus:border-green-500 text-sm shadow-sm">
    </div>

    {{-- TABLA --}}
    <article class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Concepto
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Aplicado A (Alcance)
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Tipo Socio
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Monto (Bs)
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($tarifas as $tarifa)
                        <tr class="hover:bg-gray-50 transition-colors duration-150"
                            wire:key="tarifa-{{ $tarifa->id }}">

                            {{-- Columna Concepto --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $tarifa->concepto->nombre }}</p>
                                @if ($tarifa->concepto->es_periodico)
                                    <span
                                        class="text-[10px] bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded border border-blue-100">Mensual</span>
                                @else
                                    <span
                                        class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded border border-gray-200">Pago
                                        Único</span>
                                @endif
                            </td>

                            {{-- Columna Alcance (Colegio o SIB) --}}
                            <td class="px-5 py-4">
                                @if ($tarifa->id_colegio)
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                            Específico
                                        </span>
                                        <span class="text-sm text-gray-600">{{ $tarifa->colegio->nombre }}</span>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        Global (SIB Nacional)
                                    </span>
                                @endif
                            </td>

                            {{-- Columna Tipo Socio --}}
                            <td class="px-5 py-4 text-sm text-gray-600">
                                {{ $tarifa->tipoSocio->nombre }}
                            </td>

                            {{-- Columna Monto --}}
                            <td class="px-5 py-4 text-right">
                                <span
                                    class="text-sm font-bold text-emerald-600">{{ number_format($tarifa->monto, 2) }}</span>
                            </td>

                            {{-- Columna Acciones --}}
                            <td class="px-5 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 items-center">
                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $tarifa->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 p-1 hover:bg-indigo-50 rounded transition"
                                        title="Editar">
                                        <x-ri-edit-line class="w-5 h-5" />
                                    </button>

                                    {{-- Eliminar --}}
                                    <button wire:click="delete({{ $tarifa->id }})"
                                        wire:confirm="¿Estás seguro de eliminar esta tarifa? Esto podría afectar el historial si no se maneja con cuidado."
                                        class="text-red-600 hover:text-red-900 p-1 hover:bg-red-50 rounded transition"
                                        title="Eliminar">
                                        <x-ri-delete-bin-line class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-ri-price-tag-3-line class="w-12 h-12 text-gray-300 mb-2" />
                                    <p>No hay tarifas configuradas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50">
            {{ $tarifas->links() }}
        </div>
    </article>

    {{-- MODAL (CREATE / EDIT) --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity" x-show="openModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openModal = false">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                x-show="openModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="openModal = false" type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none">
                        <x-ri-close-line class="w-6 h-6" />
                    </button>
                </div>

                <div class="sm:flex sm:items-start w-full">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-6 border-b pb-2">
                            {{ $tarifaId ? 'Editar Tarifa' : 'Nueva Tarifa' }}
                        </h3>

                        <form wire:submit.prevent="store" class="space-y-5">

                            {{-- Concepto --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Concepto de Cobro <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="id_concepto"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Seleccione un concepto...</option>
                                    @foreach ($conceptos as $con)
                                        <option value="{{ $con->id }}">{{ $con->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('id_concepto')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Tipo Socio --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Socio <span
                                        class="text-red-500">*</span></label>
                                <select wire:model="id_tipo_socio"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Seleccione tipo...</option>
                                    @foreach ($tipos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('id_tipo_socio')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Colegio (Alcance) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alcance (Colegio o SIB)
                                    <span class="text-red-500">*</span></label>
                                <select wire:model="id_colegio"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">-- Tarifa Global (SIB Nacional) --</option>
                                    <optgroup label="Colegios Específicos">
                                        @foreach ($colegios as $col)
                                            <option value="{{ $col->id }}">{{ $col->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Deja en "Global" si el precio aplica a todos, o
                                    selecciona un colegio específico.</p>
                                @error('id_colegio')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Monto --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monto (Bs) <span
                                        class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">Bs</span>
                                    </div>
                                    <input type="number" step="0.01" wire:model="monto"
                                        class="focus:ring-green-500 focus:border-green-500 block w-full pl-8 sm:text-sm border-gray-300 rounded-md"
                                        placeholder="0.00">
                                </div>
                                @error('monto')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Botones --}}
                            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                                <button type="button" @click="openModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    Cancelar
                                </button>
                                <button type="submit" wire:loading.attr="disabled"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none disabled:opacity-50 flex items-center">
                                    <span wire:loading.remove>{{ $tarifaId ? 'Actualizar' : 'Guardar' }}</span>
                                    <span wire:loading class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Procesando...
                                    </span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
