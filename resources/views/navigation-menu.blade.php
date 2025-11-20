<nav x-data="{ open: false, openColegios: false }" class="bg-white shadow-md fixed w-full z-50">
    <div class="max-w-full mx-auto px-2 sm:px-4 lg:px-4">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="https://sibpotosi.org/" class="text-2xl font-bold text-green-800">
                    <img src="{{ Storage::url('logo/logoSIB0.png') }}" alt="Logo S.I.B - Potosí" class="h-16 w-16 ">
                </a>
            </div>

            <!-- Menú Desktop -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                @auth
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                @endauth

                <!-- Colegios Dropdown -->
                <div class="relative" x-data="{ openColegios: false }" @mouseenter="openColegios = true"
                    @mouseleave="openColegios = false">
                    <button
                        class="px-4 py-2 rounded-md text-gray-700 hover:text-white hover:bg-green-700 transition flex items-center">
                        Colegios
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="openColegios" x-transition x-cloak
                        class="absolute mt-2 w-60 bg-white border rounded-lg shadow-lg overflow-hidden">
                        <ul>
                            @foreach (\App\Models\Colegio::activo()->get() as $colegio)
                                <li class="block">
                                    <x-responsive-nav-link href="{{ route('colegios.show', $colegio->slug) }}"
                                        wire:navigate.hover :active="request()->route('slug') === $colegio->slug">
                                        {{ $colegio->nombre_limpio }}
                                    </x-responsive-nav-link>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button
                                    class="px-4 py-2 rounded-md text-gray-700 hover:text-white hover:bg-green-700 transition">
                                    {{ Auth::user()->currentTeam->name }}
                                    <svg class="ml-1 h-4 w-4 inline" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-56">
                                    <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Team') }}</div>
                                    <x-dropdown-link
                                        href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">{{ __('Team Settings') }}</x-dropdown-link>
                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link
                                            href="{{ route('teams.create') }}">{{ __('Create New Team') }}</x-dropdown-link>
                                    @endcan
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                @auth
                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="h-8 w-8 rounded-full object-cover"
                                            src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <button
                                        class="px-4 py-2 rounded-md text-gray-700 hover:text-white hover:bg-green-700 transition flex items-center">
                                        {{ Auth::user()->name }}
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Administre su cuenta') }}</div>
                                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Perfil') }}</x-dropdown-link>
                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <x-dropdown-link
                                        href="{{ route('api-tokens.index') }}">{{ __('API Tokens') }}</x-dropdown-link>
                                @endif
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                        @click.prevent="$root.submit();">{{ __('Cerrar Sesión') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endauth
            </div>

            <!-- Hamburger Mobile -->
            <div class="flex sm:hidden">
                <button @click="open = ! open"
                    class="p-2 rounded-md text-gray-700 hover:text-white hover:bg-green-700 transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link href="{{ route('dashboard') }}"
                    :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            @endauth
            @foreach (\App\Models\Colegio::activo()->orderBy('nombre')->get() as $colegio)
                <x-responsive-nav-link href="{{ route('colegios.show', $colegio->slug) }}" wire:navigate.hover
                    :active="request()->route('slug') === $colegio->slug">
                    {{ $colegio->nombre_limpio }}
                </x-responsive-nav-link>
            @endforeach

        </div>
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="flex items-center px-4">
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}">
                    @endif
                    <div class="ml-3">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link href="{{ route('profile.show') }}"
                        :active="request()->routeIs('profile.show')">{{ __('Profile') }}</x-responsive-nav-link>
                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}"
                            :active="request()->routeIs('api-tokens.index')">{{ __('API Tokens') }}</x-responsive-nav-link>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" x-data>@csrf
                        <x-responsive-nav-link href="{{ route('logout') }}"
                            @click.prevent="$root.submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>
