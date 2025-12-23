<div x-data="{ openModal: @entangle('openModal') }" x-init="$watch('openModal', value => { if (!value) { $wire.call('resetFields'); } })" class="p-6 font-sans">

    {{-- ENCABEZADO --}}
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Cuentas Bancarias</h1>
            <p class="text-sm text-slate-500 mt-1">Administra las cuentas y QRs para la recepción de pagos.</p>
        </div>
        <button @click="openModal = true" wire:click="create"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all hover:shadow-md flex items-center gap-2 text-sm font-medium">
            <x-ri-bank-card-line class="w-4 h-4" />
            Nueva Cuenta
        </button>
    </header>

    {{-- FEEDBACK --}}
    @if (session()->has('success'))
        <div
            class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-ri-checkbox-circle-line class="w-5 h-5" />
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            <x-ri-close-line class="w-5 h-5 cursor-pointer hover:text-emerald-900"
                onclick="this.parentElement.remove()" />
        </div>
    @endif

    {{-- BÚSQUEDA --}}
    <div class="mb-4">
        <div class="relative w-full sm:w-1/3">
            <x-ri-search-line class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar Banco, Cuenta o Titular..."
                class="w-full pl-9 rounded-lg border-slate-300 focus:ring-slate-800 focus:border-slate-800 text-sm shadow-sm">
        </div>
    </div>

    {{-- TABLA --}}
    <article class="bg-white shadow-md rounded-xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead class="bg-slate-50">
                    <tr>
                        <th
                            class="px-6 py-3 border-b border-slate-200 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Banco / Cuenta
                        </th>
                        <th
                            class="px-6 py-3 border-b border-slate-200 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Alcance (Colegio)
                        </th>
                        <th
                            class="px-6 py-3 border-b border-slate-200 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Titular
                        </th>
                        <th
                            class="px-6 py-3 border-b border-slate-200 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                            QR
                        </th>
                        <th
                            class="px-6 py-3 border-b border-slate-200 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Estado / Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($cuentas as $cuenta)
                        <tr
                            class="hover:bg-slate-50 transition-colors duration-150 {{ !$cuenta->activo ? 'bg-slate-50 opacity-60 grayscale' : '' }}">

                            {{-- Columna Banco --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                        <x-ri-bank-line class="w-5 h-5" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-slate-900">{{ $cuenta->banco }}</p>
                                        <p class="text-xs font-mono text-slate-500">{{ $cuenta->nro_cuenta }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna Alcance --}}
                            <td class="px-6 py-4">
                                @if ($cuenta->id_colegio)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        {{ $cuenta->colegio->nombre }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                        Global (SIB Nacional)
                                    </span>
                                @endif
                            </td>

                            {{-- Columna Titular --}}
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $cuenta->titular }}
                            </td>

                            {{-- Columna QR --}}
                            <td class="px-6 py-4 text-center">
                                @if ($cuenta->qr_path)
                                    <img src="{{ Storage::url($cuenta->qr_path) }}"
                                        class="h-16 w-16 object-cover border border-slate-200" alt="QR">
                                @else
                                    <span class="text-xs text-slate-400 italic">Sin QR</span>
                                @endif
                            </td>

                            {{-- Columna Acciones --}}
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-2 items-center">
                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $cuenta->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 p-1.5 hover:bg-indigo-50 rounded transition"
                                        title="Editar">
                                        <x-ri-edit-line class="w-5 h-5" />
                                    </button>

                                    {{-- Toggle Estado --}}
                                    <button wire:click="toggleStatus({{ $cuenta->id }})"
                                        class="p-1.5 rounded transition {{ $cuenta->activo ? 'text-red-500 hover:bg-red-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                        title="{{ $cuenta->activo ? 'Desactivar Cuenta' : 'Activar Cuenta' }}">
                                        @if ($cuenta->activo)
                                            <x-ri-toggle-fill class="w-6 h-6 text-emerald-500" />
                                        @else
                                            <x-ri-toggle-line class="w-6 h-6 text-slate-400" />
                                        @endif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-ri-bank-card-2-line class="w-10 h-10 text-slate-300 mb-2" />
                                    <p>No se encontraron cuentas bancarias.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $cuentas->links() }}
        </div>
    </article>

    {{-- MODAL --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" x-show="openModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openModal = false">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                x-show="openModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="openModal = false" type="button"
                        class="bg-white rounded-md text-slate-400 hover:text-slate-600 focus:outline-none">
                        <x-ri-close-line class="w-6 h-6" />
                    </button>
                </div>

                <div class="sm:flex sm:items-start w-full">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-bold text-slate-900 mb-6 pb-4 border-b border-slate-100">
                            {{ $cuentaId ? 'Editar Cuenta' : 'Nueva Cuenta Bancaria' }}
                        </h3>

                        <form wire:submit.prevent="store" class="space-y-5">

                            {{-- Alcance --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Pertenece a
                                    (Alcance)</label>
                                <select wire:model="id_colegio"
                                    class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    <option value="">-- SIB Nacional (Global) --</option>
                                    <optgroup label="Colegios Específicos">
                                        @foreach ($colegios as $col)
                                            <option value="{{ $col->id }}">{{ $col->nombre }}</option>
                                        @endforeach
                                    </optgroup>
                                </select>
                                <p class="text-xs text-slate-500 mt-1">Si dejas "SIB Nacional", esta cuenta aparecerá
                                    para todos.</p>
                                @error('id_colegio')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Banco y Nro --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Banco <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="banco" placeholder="Ej: Banco Unión"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('banco')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nro. Cuenta <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nro_cuenta"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('nro_cuenta')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Titular --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Titular de la Cuenta <span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model="titular"
                                    class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                @error('titular')
                                    <span class="text-xs text-red-600">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- QR Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Imagen QR
                                    (Opcional)</label>

                                <div class="flex items-center gap-4">
                                    @if ($qr_imagen)
                                        {{-- Preview Nueva --}}
                                        <div class="relative">
                                            <img src="{{ $qr_imagen->temporaryUrl() }}"
                                                class="h-20 w-20 rounded-lg object-cover border border-slate-200">
                                            <span
                                                class="absolute -top-2 -right-2 bg-emerald-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">Nuevo</span>
                                        </div>
                                    @elseif($qr_path_actual)
                                        {{-- Preview Actual --}}
                                        <img src="{{ Storage::url($qr_path_actual) }}"
                                            class="h-20 w-20 rounded-lg object-cover border border-slate-200">
                                    @else
                                        {{-- Placeholder --}}
                                        <div
                                            class="h-20 w-20 rounded-lg bg-slate-50 border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-400">
                                            <x-ri-qr-code-line class="w-8 h-8" />
                                        </div>
                                    @endif

                                    <div class="flex-1">
                                        <input type="file" wire:model="qr_imagen" accept="image/*"
                                            class="block w-full text-sm text-slate-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-slate-100 file:text-slate-700
                                            hover:file:bg-slate-200">
                                        <p class="text-xs text-slate-500 mt-1">PNG, JPG hasta 2MB.</p>
                                    </div>
                                </div>
                                @error('qr_imagen')
                                    <span class="text-xs text-red-600 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Estado --}}
                            <div class="flex items-center gap-2 pt-2">
                                <input type="checkbox" wire:model="activo" id="activo"
                                    class="rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                <label for="activo" class="text-sm text-slate-700 font-medium">Cuenta Activa</label>
                            </div>

                            {{-- Footer --}}
                            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                                <button type="button" @click="openModal = false"
                                    class="px-4 py-2 border border-slate-300 rounded-md text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition">Cancelar</button>
                                <button type="submit" wire:loading.attr="disabled"
                                    class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition flex items-center">
                                    <span
                                        wire:loading.remove>{{ $cuentaId ? 'Guardar Cambios' : 'Registrar Cuenta' }}</span>
                                    <span wire:loading class="flex items-center gap-2"><svg
                                            class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>Procesando...</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
