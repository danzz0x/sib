@props(['colegio'])
@props(['secciones'])

<div x-data="{ openModal: false, openModalPub: false, openModalDetalle: false, openModalDirec: false }" x-cloak>
    @if ($secciones->isNotEmpty())
        <div class="space-y-8">
            @foreach ($secciones->sortBy('orden') as $seccion)
                <div wire:key="seccion-{{ $seccion->id }}" class="relative overflow-hidden">
                    <div class="px-6 py-2 relative ">
                        <div class="flex justify-center">
                            <div class="flex items-center space-x-3">
                                @if ($seccion->titulo)
                                    <h2
                                        class="text-xl md:text-4xl text-center font-semibold uppercase tracking-[0.15em] text-gray-800 relative">
                                        <span class="relative z-10 px-4">{{ $seccion->titulo }}</span>
                                        <span
                                            class="absolute left-1/2 -bottom-2 w-16 h-[3px] bg-green-700 rounded-full -translate-x-1/2"></span>
                                    </h2>
                                @endif
                                @auth
                                    <span class="px-2 py-1 text-white  rounded-full text-xs bg-green-900">
                                        {{ ucfirst($seccion->tipo_mostrar->value) }}
                                    </span>
                                @endauth
                            </div>
                            @auth
                                <div class="flex items-center space-x-2">
                                    <div class="flex flex-col space-y-1">
                                        @if ($seccion->orden > 1)
                                            <button wire:click="$dispatch('move-seccion-up', { id: {{ $seccion->id }} })"
                                                class="p-1 rounded hover:bg-white/20 transition-colors group"
                                                title="Mover hacia arriba">
                                                <x-ri-arrow-up-line
                                                    class="w-4 h-4 text-black  group-hover:text-yellow-200" />
                                            </button>
                                        @else
                                            <div class="p-1">
                                                <x-ri-arrow-up-line class="w-4 h-4 text-gray-400" />
                                            </div>
                                        @endif

                                        @if ($seccion->orden < $secciones->max('orden'))
                                            <button wire:click="$dispatch('move-seccion-down', { id: {{ $seccion->id }} })"
                                                class="p-1 rounded hover:bg-white/20 transition-colors group"
                                                title="Mover hacia abajo">
                                                <x-ri-arrow-down-line
                                                    class="w-4 h-4 text-black group-hover:text-yellow-200" />
                                            </button>
                                        @else
                                            <div class="p-1">
                                                <x-ri-arrow-down-line class="w-4 h-4 text-gray-400" />
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Menú de opciones --}}
                                    <div class="relative" x-data="{ open: false }">

                                        <button @click="open = !open"
                                            class="p-2 rounded-full hover:bg-white/20 transition-colors">
                                            <x-ri-more-2-line class="w-5 h-5 " />
                                        </button>


                                        <div x-show="open" @click.away="open = false"
                                            class="absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-20"
                                            x-transition>
                                            <button @click="openModal = !openModal; open = false"
                                                wire:click="$dispatch('edit-seccion', { id: {{ $seccion->id }} })"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <x-ri-edit-line class="w-4 h-4 mr-3" />
                                                Editar Sección
                                            </button>
                                            <button
                                                onclick="window.confirmDelete({{ $seccion->id }}, (id) => Livewire.dispatch('delete-seccion', { id: id }), '¿Estás seguro de eliminar esta sección? Se eliminarán también todas sus publicaciones.')"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <x-ri-delete-bin-2-line class="w-4 h-4 mr-3" />
                                                Eliminar Sección
                                            </button>
                                            <hr class="my-1">
                                            <button
                                                @click="{{ $seccion->tipo_mostrar->value === 'directorio' ? 'openModalDirec = true' : 'openModalPub = true' }}"
                                                wire:click="dispatch('create-pub', {id: {{ $seccion->id }}})"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-green-900 hover:bg-blue-50">
                                                <x-ri-image-add-line class="w-4 h-4 mr-3" />
                                                Agregar publicacion
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            @endauth
                        </div>

                        @auth
                            {{-- Indicador de orden --}}
                            <div class="absolute top-2 left-2">
                                <div class="bg-white/20 rounded-full px-2 py-1 text-xs font-medium">
                                    #{{ $seccion->orden }}
                                </div>
                            </div>
                        @endauth
                    </div>

                    {{-- Contenido de la sección --}}
                    <div class="p-2 bg-white">
                        @if ($seccion->publicaciones->isNotEmpty())
                            @switch($seccion->tipo_mostrar->value)
                                @case('carrusel')
                                    <x-colegio.tipos.carrusel :seccion="$seccion" />
                                @break

                                @case('cuadricula')
                                    <x-colegio.tipos.cuadricula :seccion="$seccion" />
                                @break

                                @case('lista')
                                    <x-colegio.tipos.lista :seccion="$seccion" />
                                @break

                                @case('directorio')
                                    <x-colegio.tipos.directorio :seccion="$seccion" />
                                @break
                            @endswitch
                        @else
                            <div class="text-center py-12 text-gray-500">
                                <x-ri-file-list-3-line class="w-12 h-12 mx-auto mb-4 text-gray-300" />
                                <p class="text-lg font-medium">No hay publicaciones en esta sección</p>
                                <p class="text-sm">Las publicaciones aparecerán aquí cuando las agregues</p>
                                @auth
                                    <button
                                        @click="{{ $seccion->tipo_mostrar->value === 'directorio' ? 'openModalDirec = true' : 'openModalPub = true' }}"
                                        wire:click="dispatch('create-pub', {id: {{ $seccion->id }}})"
                                        class="inline-flex items-center mt-4 px-4 py-2 bg-[#213502] text-white rounded-lg hover:bg-[#2d4a03] transition-colors">
                                        <x-ri-add-line class="w-4 h-4 mr-2" />
                                        Agregar Primera Publicación
                                    </button>
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-gray-50 rounded-lg">
            <x-ri-layout-grid-line class="w-16 h-16 mx-auto mb-6 text-gray-300" />
            <h3 class="text-xl font-medium text-gray-900 mb-2">No hay secciones creadas</h3>
            <p class="text-gray-600 mb-6">Comienza organizando el contenido de tu colegio creando la primera sección</p>

            @auth
                <button @click="openModal = !openModal"
                    class="inline-flex items-center px-6 py-3 bg-[#213502] text-white rounded-lg hover:bg-[#2d4a03] font-medium transition-colors">
                    <x-ri-add-line class="w-5 h-5 mr-2" />
                    Crear Primera Sección
                </button>
            @endauth
        </div>
    @endif

    @auth
        {{-- Botón flotante para agregar nueva sección (cuando ya hay secciones) --}}
        @if ($secciones->isNotEmpty())
            <div class="fixed bottom-6 right-6 z-30">
                <button @click="openModal = !openModal"
                    class="p-4 bg-[#213502] text-white rounded-full shadow-lg hover:bg-[#2d4a03] transition-colors hover:shadow-xl"
                    title="Agregar Nueva Sección">
                    <x-ri-add-line class="w-6 h-6" />
                </button>
            </div>
        @endif

        {{-- Modales de CRUD --}}
        @livewire('secciones.crud', ['colegio' => $colegio], key('secciones-modal-' . $colegio->id))
        @livewire('publicaciones.crud', ['colegio' => $colegio], key('publicaciones-modal-' . $colegio->id))
    @endauth
</div>
