<div x-data="{ openModal: @entangle('openModal') }" x-init="$watch('openModal', value => {
    if (!value) { $wire.call('resetFields'); }
})" class="p-6">

    {{-- ENCABEZADO --}}
    <header class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
            <p class="text-sm text-gray-500 mt-1">Administra los accesos y roles del sistema.</p>
        </div>
        <button @click="openModal = true" wire:click="create"
            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md flex items-center gap-2">
            <x-ri-add-line class="w-4 h-4" />
            Nuevo Usuario
        </button>
    </header>

    <article class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Usuario / Estado
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Rol Global
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Asignaciones
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-50 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $user)
                        {{-- Añadimos opacidad si está inactivo para feedback visual rápido --}}
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ !$user->activo ? 'bg-gray-50 opacity-75 grayscale-[50%]' : '' }}"
                            wire:key="user-{{ $user->id }}">

                            {{-- Columna Nombre/Email + Estado --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center">
                                    {{-- Avatar o Icono --}}
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full {{ $user->activo ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500' }} flex items-center justify-center">
                                        <span class="font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                    </div>

                                    <div class="ml-3">
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-semibold text-gray-900 whitespace-no-wrap">
                                                {{ $user->name }}
                                            </p>

                                            {{-- BADGE DE ESTADO --}}
                                            @if (!$user->activo)
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 whitespace-no-wrap">
                                            {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                @php
                                    $badge = match ($user->role) {
                                        App\Models\User::ROLE_ADMIN => [
                                            'label' => 'Admin General',
                                            'class' => 'bg-purple-100 text-purple-800',
                                        ],
                                        App\Models\User::ROLE_CAJERO => [
                                            'label' => 'Cajero General',
                                            'class' => 'bg-orange-100 text-orange-800',
                                        ],
                                        default => [
                                            'label' => 'Usuario Colegio',
                                            'class' => 'bg-blue-100 text-blue-800',
                                        ],
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>

                            <td class="px-5 py-4">
                                @if ($user->colegios->isNotEmpty())
                                    <div class="flex flex-col gap-1.5">
                                        @foreach ($user->colegios as $colegio)
                                            <div
                                                class="flex items-center justify-between bg-white border border-gray-200 px-2 py-1 rounded text-xs max-w-xs shadow-sm">
                                                <span class="font-medium text-gray-700 truncate mr-2">
                                                    {{ $colegio->nombre }}
                                                </span>
                                                <span
                                                    class="text-gray-500 bg-gray-50 px-1 rounded border border-gray-100">
                                                    {{ ucfirst($colegio->pivot->tipo_usuario_colegio ?? '-') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">Sin asignaciones</span>
                                @endif
                            </td>

                            {{-- Columna Acciones --}}
                            <td class="px-5 py-4 text-right text-sm font-medium">
                                <div class="flex justify-end gap-3 items-center">

                                    {{-- Botón Editar --}}
                                    <button wire:click="edit({{ $user->id }})"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors p-1 hover:bg-indigo-50 rounded"
                                        title="Editar Datos">
                                        <x-ri-edit-line class="w-5 h-5" />
                                    </button>

                                    @if (auth()->id() !== $user->id)
                                        {{-- Botón ACTIVAR / DESACTIVAR --}}
                                        <button {{-- Usamos wire:confirm nativo de Livewire 3/Laravel 12 --}} wire:click="toggleStatus({{ $user->id }})"
                                            wire:confirm="¿Estás seguro de que deseas {{ $user->activo ? 'desactivar' : 'activar' }} a este usuario?"
                                            class="transition-colors p-1 rounded hover:bg-opacity-50
                                            {{ $user->activo ? 'text-red-600 hover:bg-red-50' : 'text-green-600 hover:bg-green-50' }}"
                                            title="{{ $user->activo ? 'Desactivar acceso' : 'Activar acceso' }}">

                                            @if ($user->activo)
                                                {{-- Icono Bloquear (Si está activo) --}}
                                                <x-ri-user-forbid-line class="w-5 h-5" />
                                            @else
                                                {{-- Icono Activar (Si está inactivo) --}}
                                                <x-ri-checkbox-circle-line class="w-5 h-5" />
                                            @endif
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <x-ri-user-unfollow-line class="w-12 h-12 text-gray-300 mb-2" />
                                    <p>No se encontraron usuarios registrados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    {{-- MODAL (El código del modal se mantiene IGUAL que el que me pasaste, no cambia nada ahí) --}}
    <div x-show="openModal" @keydown.escape.window="openModal = false" x-cloak
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"
                @click="openModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full sm:p-6"
                x-show="openModal" x-transition:enter="ease-out duration-300"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="absolute top-0 right-0 pt-4 pr-4">

                    <button @click="openModal = false" type="button"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">

                        <span class="sr-only">Cerrar</span>

                        <x-ri-close-circle-line class="w-6 h-6" />

                    </button>

                </div>


                <div class="sm:flex sm:items-start w-full">

                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">

                        <h3 class="text-xl leading-6 font-bold text-gray-900 mb-5" id="modal-title">

                            {{ $userId ? 'Editar Usuario' : 'Crear Nuevo Usuario' }}

                        </h3>


                        <form wire:submit.prevent="store" class="space-y-4">


                            {{-- GRID: Nombre y Email --}}

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>

                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span
                                            class="text-red-500">*</span></label>

                                    <input type="text" wire:model="name"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                        placeholder="Juan Pérez">

                                    @error('name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>


                                <div>

                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                            class="text-red-500">*</span></label>

                                    <input type="email" wire:model="email"
                                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                        placeholder="juan@ejemplo.com">

                                    @error('email')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>

                            </div>


                            {{-- Rol Global --}}

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">Rol Global en el Sistema

                                    <span class="text-red-500">*</span></label>

                                <select wire:model.live="role"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">

                                    <option value="#">Seleccion un rol</option>

                                    <option value="{{ App\Models\User::ROLE_COLEGIO }}">Usuario de Colegio

                                        (Restringido)</option>
                                    <option value="{{ App\Models\User::ROLE_CAJERO }}">Cajero general</option>

                                    <option value="{{ App\Models\User::ROLE_ADMIN }}">Administrador General (Acceso

                                        Total)</option>

                                </select>

                                @error('role')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- Password --}}

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">

                                    Contraseña {{ $userId ? '(Opcional)' : '*' }}

                                </label>

                                <input type="password" wire:model="password"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"
                                    placeholder="{{ $userId ? 'Dejar vacío para mantener la actual' : 'Mínimo 8 caracteres' }}">

                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            <div x-show="$wire.role === '{{ App\Models\User::ROLE_COLEGIO }}'"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="pt-4 mt-2 border-t border-gray-100">


                                <div class="flex justify-between items-center mb-3">

                                    <label class="block text-sm font-bold text-gray-700">Colegios y Cargos

                                        Asignados</label>

                                    <button type="button" wire:click="addColegio"
                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">

                                        <x-ri-add-circle-line class="w-4 h-4 mr-1" />

                                        Agregar Fila

                                    </button>

                                </div>


                                <div
                                    class="space-y-3 bg-gray-50 p-3 rounded-lg border border-gray-200 max-h-60 overflow-y-auto custom-scrollbar">

                                    @foreach ($userColegios as $index => $item)
                                        <div class="flex items-start gap-2" wire:key="row-{{ $index }}">


                                            {{-- Select Colegio --}}

                                            <div class="flex-1">

                                                <select wire:model="userColegios.{{ $index }}.colegio_id"
                                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs sm:text-sm py-2">

                                                    <option value="">Seleccionar Colegio...</option>

                                                    @foreach ($colegiosOptions as $colegio)
                                                        <option value="{{ $colegio->id }}">{{ $colegio->nombre }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                                @error("userColegios.{$index}.colegio_id")
                                                    <span
                                                        class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                                @enderror

                                            </div>


                                            <div class="w-1/3 sm:w-1/4">

                                                <select wire:model="userColegios.{{ $index }}.role"
                                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 text-xs sm:text-sm py-2">

                                                    <option value="">Cargo...</option>

                                                    <option value="director">Director</option>

                                                    <option value="tesorero">Tesorero</option>

                                                    <option value="publicador">Publicador</option>

                                                    <option value="miembro">Miembro</option>

                                                </select>

                                                @error("userColegios.{$index}.role")
                                                    <span
                                                        class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                                @enderror

                                            </div>


                                            {{-- Eliminar Fila --}}

                                            <button type="button" wire:click="removeColegio({{ $index }})"
                                                class="mt-1.5 text-gray-400 hover:text-red-500 transition-colors"
                                                title="Quitar asignación">

                                                <x-ri-delete-bin-line class="w-5 h-5" />

                                            </button>

                                        </div>
                                    @endforeach


                                    @if (empty($userColegios))
                                        <div class="text-center py-4">

                                            <p class="text-sm text-gray-500">Este usuario no tiene colegios asignados.

                                            </p>

                                            <p class="text-xs text-gray-400">Presiona "Agregar Fila" para comenzar.</p>

                                        </div>
                                    @endif

                                </div>

                                @error('userColegios')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                            </div>


                            {{-- Footer del Formulario --}}

                            <div
                                class="mt-6 sm:mt-8 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense pt-4 border-t border-gray-100">

                                <button type="submit" wire:loading.attr="disabled" wire:target="store"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:col-start-2 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">


                                    <span wire:loading.remove wire:target="store">

                                        {{ $userId ? 'Guardar Cambios' : 'Crear Usuario' }}

                                    </span>


                                    <span wire:loading wire:target="store" class="flex items-center">

                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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


                                <button type="button" @click="openModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:mt-0 sm:col-start-1 sm:text-sm">

                                    Cancelar

                                </button>

                            </div>


                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
