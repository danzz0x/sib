@if ($paginator->hasPages())
    {{-- La variable $pageName es crucial para la paginación múltiple. --}}
    @php
        $pageName = $paginator->getPageName();
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center p-4">
        <div class="flex items-center space-x-2 sm:space-x-3">

            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <span
                    class="px-4 py-2 text-sm font-semibold text-gray-500 bg-gray-100 rounded-lg cursor-not-allowed transition duration-150">
                    ←
                </span>
            @else
                <button wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled"
                    dusk="previousPage{{ $pageName == 'page' ? '' : '.' . $pageName }}"
                    class="px-4 py-2 text-sm font-semibold text-white bg-[#213502] rounded-lg shadow-md hover:bg-[#2d4a03] focus:outline-none focus:ring-2 focus:ring-[#213502] focus:ring-opacity-50 transition duration-150 ease-in-out">
                    ←
                </button>
            @endif

            {{-- Números de página --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-sm font-medium text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Botón Activo --}}
                            <span aria-current="page"
                                class="px-4 py-2 text-sm font-bold text-white bg-[#213502] rounded-lg shadow-lg cursor-default">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Botón Inactivo --}}
                            <button wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-opacity-50">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled"
                    dusk="nextPage{{ $pageName == 'page' ? '' : '.' . $pageName }}"
                    class="px-4 py-2 text-sm font-semibold text-white bg-[#213502] rounded-lg shadow-md hover:bg-[#2d4a03] focus:outline-none focus:ring-2 focus:ring-[#213502] focus:ring-opacity-50 transition duration-150 ease-in-out">
                    →
                </button>
            @else
                <span
                    class="px-4 py-2 text-sm font-semibold text-gray-500 bg-gray-100 rounded-lg cursor-not-allowed transition duration-150">
                    →
                </span>
            @endif
        </div>
    </nav>
@endif
