<nav x-data="{ openMobile: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)"
    :class="{
        'bg-white/90 backdrop-blur-md shadow-md border-b border-gray-200/50': scrolled,
        'bg-white border-b border-gray-100':
            !scrolled
    }"
    class="fixed w-full z-50 transition-all duration-300 font-sans">

    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- LOGOTIPO --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="https://sibpotosi.org/" class="flex items-center gap-3 group">
                    <img src="{{ Storage::url('logo/logoSIB0.png') }}" alt="Logo S.I.B"
                        class="h-10 w-auto transition-transform duration-300 group-hover:scale-105 filter drop-shadow-sm">
                    <div class="flex flex-col">
                        <span
                            class="font-bold text-slate-800 leading-tight text-lg group-hover:text-emerald-700 transition-colors">S.I.B</span>
                        <span
                            class="text-[10px] text-slate-500 uppercase tracking-[0.2em] leading-none group-hover:text-emerald-600 transition-colors">Potosí</span>
                    </div>
                </a>
            </div>

            {{-- MENÚ DESKTOP --}}
            <div class="hidden lg:flex lg:items-center lg:space-x-1">
                <a href="{{ route('dashboard') }}"
                    class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
                {{-- DROPDOWN COLEGIOS --}}
                <div class="relative ml-1" x-data="{ openColegios: false }" @mouseenter="openColegios = true"
                    @mouseleave="openColegios = false">
                    <button
                        class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 group transition-all">
                        <span>Colegios</span>
                        <svg class="ml-1.5 h-4 w-4 text-slate-400 group-hover:text-slate-600 transition-transform duration-200"
                            :class="{ 'rotate-180': openColegios }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openColegios" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                        class="absolute left-0 mt-2 w-72 bg-white/95 backdrop-blur-sm border border-slate-100 rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden z-50"
                        style="display: none;">

                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Selecciona un colegio
                            </p>
                        </div>

                        <div class="py-2 max-h-[60vh] overflow-y-auto custom-scrollbar">
                            @foreach (\App\Models\Colegio::activo()->orderBy('nombre')->get() as $colegio)
                                <a href="{{ route('colegios.show', $colegio->slug) }}"
                                    class="flex items-center px-4 py-3 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-800 transition-colors group {{ request()->route('slug') === $colegio->slug ? 'bg-emerald-50 text-emerald-800 font-semibold' : '' }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full mr-3 {{ request()->route('slug') === $colegio->slug ? 'bg-emerald-500' : 'bg-slate-300 group-hover:bg-emerald-400' }}"></span>
                                    {{ $colegio->nombre_limpio }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @can('access-operations')
                    <div class="h-6 w-px bg-slate-200 mx-3"></div>

                    {{-- Botón Usuarios (Admin Global) --}}
                    @can('manage-everything')
                        <a href="{{ route('admin.users') }}"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('admin.users') ? 'bg-slate-100 text-slate-900' : '' }}">
                            Usuarios
                        </a>
                    @endcan

                    {{-- Dropdown Gestión --}}
                    <div class="relative" x-data="{ openGestion: false }" @mouseenter="openGestion = true"
                        @mouseleave="openGestion = false">
                        <button
                            class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 group transition-all">
                            <span
                                class="flex items-center justify-center w-5 h-5 mr-1.5 rounded bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <span>Gestión</span>
                            <svg class="ml-1.5 h-3.5 w-3.5 text-slate-400 transition-transform duration-200"
                                :class="{ 'rotate-180': openGestion }" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="openGestion" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            class="absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-xl shadow-xl overflow-hidden z-50"
                            style="display: none;">

                            <div class="py-2">
                                <a href="{{ route('admin.pagos') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <x-ri-money-dollar-circle-line
                                        class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                    Caja / Cobros
                                </a>
                                <a href="{{ route('admin.socios') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <x-ri-user-settings-line
                                        class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                    Socios
                                </a>


                                <a href="{{ route('admin.historial') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <x-ri-file-list-3-line
                                        class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                    Historial
                                </a>
                                @can('manage-settings')
                                    <a href="{{ route('admin.tarifas') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <x-ri-price-tag-3-line
                                            class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                        Tarifas
                                    </a>
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <a href="{{ route('admin.cuentas') }}"
                                        class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                        <x-ri-bank-line class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                        Cuentas Bancarias
                                    </a>
                                @endcan
                                <a href="{{ route('admin.validar') }}"
                                    class="group flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <x-ri-check-double-line
                                        class="w-5 h-5 mr-3 text-slate-400 group-hover:text-emerald-500" />
                                    Validar Pagos
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center ml-4 pl-4 border-l border-slate-200">
                        <livewire:admin.global.notification-bell />
                    </div>
                @endcan
            </div>

            {{-- ZONA DERECHA (PERFIL / LOGIN / MENU MÓVIL) --}}
            <div class="flex items-center gap-3">

                @auth
                    {{-- DROPDOWN PERFIL (Desktop) --}}
                    <div class="hidden lg:flex relative ml-2">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center gap-2 text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-emerald-300 transition pl-1 pr-2 py-1 hover:bg-slate-50">
                                    <img class="h-8 w-8 rounded-full object-cover ring-2 ring-white shadow-sm"
                                        src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    <span
                                        class="text-xs font-semibold text-slate-700">{{ explode(' ', Auth::user()->name)[0] }}</span>
                                    <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="block px-4 py-3 text-xs text-slate-400 border-b border-slate-50">
                                    Conectado como <br> <span
                                        class="font-bold text-slate-600">{{ Auth::user()->email }}</span>
                                </div>
                                <x-dropdown-link href="{{ route('profile.show') }}"
                                    class="hover:bg-emerald-50 hover:text-emerald-700">Perfil</x-dropdown-link>
                                <div class="border-t border-slate-100"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                        class="text-red-600 hover:bg-red-50 hover:text-red-700">Cerrar
                                        Sesión</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden lg:flex items-center gap-3">
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold text-slate-600 hover:text-emerald-600 px-4 py-2 rounded-lg hover:bg-slate-50 transition-colors">Ingresar</a>
                    </div>
                @endauth

                {{-- HAMBURGUESA MÓVIL --}}
                <div class="flex items-center lg:hidden">
                    <button @click="openMobile = ! openMobile"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 transition-all">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': openMobile, 'inline-flex': !openMobile }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !openMobile, 'inline-flex': openMobile }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MENÚ MÓVIL EXPANDIBLE --}}
    <div x-show="openMobile" x-collapse
        class="lg:hidden bg-white border-t border-slate-100 shadow-xl overflow-y-auto max-h-[85vh]">

        <div class="pt-3 pb-6 space-y-1 px-4">
            @auth
                {{-- Perfil Resumido Móvil --}}
                <div class="flex items-center px-2 pb-4 mb-4 border-b border-slate-100">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full object-cover ring-2 ring-emerald-100"
                            src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                    <div class="ml-3">
                        <div class="font-bold text-base text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="ml-auto">
                        <livewire:admin.global.notification-bell />
                    </div>
                </div>

                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" class="rounded-lg">
                    Dashboard
                </x-responsive-nav-link>
            @endauth

            {{-- Colegios Móvil (Público/Auth) --}}
            <div x-data="{ expandColegios: false }" class="rounded-lg overflow-hidden">
                <button @click="expandColegios = !expandColegios"
                    class="w-full flex items-center justify-between px-4 py-3 text-base font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">
                    <span>Colegios</span>
                    <svg class="h-5 w-5 transform transition-transform duration-200 text-slate-400"
                        :class="{ 'rotate-180': expandColegios }" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="expandColegios" x-collapse class="bg-slate-50 border-t border-slate-100">
                    @foreach (\App\Models\Colegio::activo()->orderBy('nombre')->get() as $colegio)
                        <a href="{{ route('colegios.show', $colegio->slug) }}"
                            class="block pl-8 pr-4 py-3 text-sm text-slate-500 hover:text-emerald-700 hover:bg-emerald-50/50 border-l-2 border-transparent hover:border-emerald-500 transition-all {{ request()->route('slug') === $colegio->slug ? 'text-emerald-700 font-bold border-emerald-500 bg-emerald-50/50' : '' }}">
                            {{ $colegio->nombre_limpio }}
                        </a>
                    @endforeach
                </div>
            </div>

            @auth
                {{-- ZONA ADMINISTRATIVA MÓVIL --}}
                @can('access-operations')
                    <div class="mt-6">
                        <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Gestión
                            Administrativa</p>
                        <div class="space-y-1">
                            {{-- Solo Super Admin --}}
                            @can('manage-everything')
                                <x-responsive-nav-link href="{{ route('admin.users') }}" :active="request()->routeIs('admin.users')" class="rounded-lg">
                                    Usuarios
                                </x-responsive-nav-link>
                            @endcan

                            {{-- Operaciones comunes (Admin, Cajero y Staff Colegio Autorizado) --}}
                            <x-responsive-nav-link href="{{ route('admin.pagos') }}" :active="request()->routeIs('admin.pagos')" class="rounded-lg">
                                Caja / Cobros
                            </x-responsive-nav-link>

                            <x-responsive-nav-link href="{{ route('admin.socios') }}" :active="request()->routeIs('admin.socios')" class="rounded-lg">
                                Socios
                            </x-responsive-nav-link>

                            <x-responsive-nav-link href="{{ route('admin.historial') }}" :active="request()->routeIs('admin.historial')"
                                class="rounded-lg">
                                Historial
                            </x-responsive-nav-link>

                            <x-responsive-nav-link href="{{ route('admin.validar') }}" :active="request()->routeIs('admin.validar')"
                                class="rounded-lg flex items-center">
                                Validar Pagos
                            </x-responsive-nav-link>

                            {{-- Solo Administradores Globales (Tarifas y Cuentas) --}}
                            @can('manage-settings')
                                <div class="pt-2 pb-1 border-t border-slate-100 mt-2">
                                    <p class="px-4 text-[9px] font-semibold text-emerald-600 uppercase tracking-wider mb-1">
                                        Configuración Global</p>
                                    <x-responsive-nav-link href="{{ route('admin.tarifas') }}" :active="request()->routeIs('admin.tarifas')"
                                        class="rounded-lg bg-emerald-50/50 text-emerald-700">
                                        Tarifas
                                    </x-responsive-nav-link>
                                    <x-responsive-nav-link href="{{ route('admin.cuentas') }}" :active="request()->routeIs('admin.cuentas')"
                                        class="rounded-lg bg-emerald-50/50 text-emerald-700 mt-1">
                                        Cuentas Bancarias
                                    </x-responsive-nav-link>
                                </div>
                            @endcan
                        </div>
                    </div>
                @endcan

                {{-- Footer del menú móvil --}}
                <div class="border-t border-slate-100 mt-6 pt-4 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}"
                        class="rounded-lg">Perfil</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();"
                            class="text-red-600 hover:bg-red-50 rounded-lg">
                            Cerrar Sesión
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="border-t border-slate-100 mt-4 pt-4 px-2">
                    <a href="{{ route('login') }}"
                        class="flex w-full justify-center items-center px-4 py-3 bg-slate-900 text-white font-bold rounded-xl shadow-lg hover:bg-slate-800 transition transform active:scale-95">
                        Ingresar al Sistema
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
