<div x-data="{ openModal: @entangle('openModal') }" x-init="$watch('openModal', value => { if (!value) { $wire.call('resetFields'); } })" class="p-6 font-sans">

    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Gestión de Socios</h1>
            <p class="text-sm text-slate-500 mt-1">Administra el padrón de ingenieros y técnicos.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <button wire:click="exportarPdf" wire:loading.attr="disabled"
                class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg shadow-sm transition-all flex items-center gap-2 text-sm font-medium disabled:opacity-50">
                <x-ri-file-pdf-2-line class="w-4 h-4 text-red-600" />
                <span>Reporte PDF</span>
                <span wire:loading wire:target="exportarPdf"
                    class="ml-1 animate-spin rounded-full h-3 w-3 border-b-2 border-slate-600"></span>
            </button>

            <button @click="openModal = true" wire:click="create"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all hover:shadow-md flex items-center gap-2 text-sm font-medium">
                <x-ri-user-add-line class="w-4 h-4" />
                Nuevo Socio
            </button>
        </div>
    </header>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Buscar</label>
                <div class="relative">
                    <x-ri-search-line class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, CI o RNI..."
                        class="w-full pl-9 rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Colegio</label>
                <select wire:model.live="filtro_colegio"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Todos los Colegios</option>
                    @foreach ($colegios as $col)
                        <option value="{{ $col->id }}">{{ $col->sigla ?? substr($col->nombre, 11, 25) . '...' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tipo Socio</label>
                <select wire:model.live="filtro_tipo"
                    class="w-full rounded-lg border-slate-300 text-sm focus:ring-slate-800 focus:border-slate-800">
                    <option value="">Todos</option>
                    @foreach ($tipos as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="limpiarFiltros"
                    class="w-full py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 rounded-lg text-sm font-medium transition flex justify-center items-center gap-1">
                    <x-ri-filter-off-line class="w-4 h-4" /> Limpiar
                </button>
            </div>
        </div>
    </div>

    {{-- FEEDBACK SUCCESS --}}
    @if (session()->has('success'))
        <div
            class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <x-ri-checkbox-circle-line class="w-5 h-5 text-emerald-600" />
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <x-ri-close-line class="w-5 h-5" />
            </button>
        </div>
    @endif

    {{-- TABLA DE SOCIOS --}}
    <article class="bg-white shadow-md rounded-xl overflow-hidden border border-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Socio
                            / ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Colegio / Esp.</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tipo /
                            Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Contacto</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($socios as $socio)
                        <tr class="hover:bg-slate-50 transition-colors duration-150 {{ $socio->estado !== 'Activo' ? 'bg-slate-50 opacity-60 grayscale' : '' }}"
                            wire:key="socio-{{ $socio->id }}">

                            {{-- Columna 1: Identificación --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full {{ $socio->estado === 'Activo' ? 'bg-slate-800 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-sm shadow-sm">
                                        {{ substr($socio->nombre, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-bold text-slate-900">{{ $socio->nombre }}</p>
                                        <div class="flex items-center gap-2 text-xs text-slate-500">
                                            <span>CI: {{ $socio->cedula }}</span>
                                            @if ($socio->rni)
                                                <span class="text-slate-300">|</span>
                                                <span
                                                    class="text-indigo-600 font-semibold bg-indigo-50 px-1 rounded">RNI:
                                                    {{ $socio->rni }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Columna 2: Colegio --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-900">{{ $socio->colegio->nombre }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $socio->especialidad->value }}</p>
                            </td>

                            {{-- Columna 3: Tipo --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    {{ $socio->tipoSocio->nombre }}
                                </span>
                                <p class="text-[10px] text-slate-400 mt-1">Reg:
                                    {{ \Carbon\Carbon::parse($socio->fecha_registro)->format('d/m/Y') }}</p>
                            </td>

                            <td class="px-6 py-4 text-sm">
                                <div class="flex flex-col gap-1.5">
                                    @if ($socio->telefono)
                                        <div class="flex items-center gap-1.5 text-xs text-slate-600">
                                            <x-ri-smartphone-line class="w-3.5 h-3.5 text-slate-400" />
                                            {{ $socio->telefono }}
                                        </div>
                                    @endif
                                    @if ($socio->email)
                                        <div class="flex items-center gap-1.5 text-xs text-slate-600 truncate max-w-[150px]"
                                            title="{{ $socio->email }}">
                                            <x-ri-mail-line class="w-3.5 h-3.5 text-slate-400" />
                                            {{ $socio->email }}
                                        </div>
                                    @endif
                                    @if (!$socio->telefono && !$socio->email)
                                        <span class="text-xs text-slate-400 italic">Sin contacto</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Columna 5: Acciones --}}
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    {{-- Editar --}}
                                    <button wire:click="edit({{ $socio->id }})"
                                        class="text-indigo-600 hover:bg-indigo-50 p-1.5 rounded transition"
                                        title="Editar Datos">
                                        <x-ri-edit-line class="w-5 h-5" />
                                    </button>

                                    {{-- Toggle Estado --}}
                                    <x-colegio.confirm-button action="toggleStatus" :id="$socio->id" :active="$socio->activo"
                                        confirm-message="¿cambiar estado del usuario?" icon-active="ri-user-forbid-line"
                                        icon-inactive="ri-user-follow-line" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <x-ri-user-search-line class="w-12 h-12 text-slate-300 mb-3" />
                                    <p class="font-medium text-slate-600">No se encontraron socios.</p>
                                    <p class="text-xs text-slate-400 mt-1">Intenta ajustar los filtros de búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $socios->links() }}
        </div>
    </article>

    {{-- MODAL (CREATE / EDIT) --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" x-show="openModal"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="openModal = false">
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Panel del Modal --}}
            <div class="relative inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-8"
                x-show="openModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                {{-- Botón Cerrar --}}
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button @click="openModal = false" type="button"
                        class="bg-white rounded-md text-slate-400 hover:text-slate-600 focus:outline-none">
                        <x-ri-close-line class="w-6 h-6" />
                    </button>
                </div>

                <div class="sm:flex sm:items-start w-full">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <h3 class="text-xl leading-6 font-bold text-slate-900 mb-6 pb-4 border-b border-slate-100">
                            {{ $socioId ? 'Editar Socio' : 'Registrar Nuevo Socio' }}
                        </h3>

                        <form wire:submit.prevent="store" class="space-y-5">

                            {{-- FILA 1: Nombre y CI --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Completo <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="nombre"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('nombre')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Cédula (CI) <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="cedula"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('cedula')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- FILA 2: Colegio y Tipo --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Colegio Profesional
                                        <span class="text-red-500">*</span></label>
                                    <select wire:model="id_colegio"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                        <option value="">Seleccione...</option>
                                        @foreach ($colegios as $col)
                                            <option value="{{ $col->id }}">{{ $col->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_colegio')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Socio <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="id_tipo_socio"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                        <option value="">Seleccione...</option>
                                        @foreach ($tipos as $tipo)
                                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_tipo_socio')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- FILA 3: RNI y Especialidad --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">RNI (Opcional)</label>
                                    <input type="text" wire:model="rni"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('rni')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Especialidad <span
                                            class="text-red-500">*</span></label>
                                    <select wire:model="especialidad"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                        <option value="">Seleccione...</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->value }}">{{ $esp->value }}</option>
                                        @endforeach
                                    </select>
                                    @error('especialidad')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- FILA 4: Contacto --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Email
                                        (Opcional)</label>
                                    <input type="email" wire:model="email"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('email')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono
                                        (Opcional)</label>
                                    <input type="text" wire:model="telefono"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('telefono')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- FILA 5: Fecha y Estado --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha Registro</label>
                                    <input type="date" wire:model="fecha_registro"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                    @error('fecha_registro')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                                    <select wire:model="estado"
                                        class="block w-full border-slate-300 rounded-md shadow-sm focus:ring-slate-800 focus:border-slate-800 sm:text-sm">
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                    </select>
                                    @error('estado')
                                        <span class="text-xs text-red-600">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- BOTONES --}}
                            <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                                <button type="button" @click="openModal = false"
                                    class="px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none transition">
                                    Cancelar
                                </button>
                                <button type="submit" wire:loading.attr="disabled"
                                    class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none disabled:opacity-50 flex items-center transition">
                                    <span
                                        wire:loading.remove>{{ $socioId ? 'Guardar Cambios' : 'Registrar Socio' }}</span>
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
