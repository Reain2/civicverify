<nav x-data="{ open: false }" class="bg-blue-700 border-b border-blue-800">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="text-2xl">🏛️</span>
                        <span class="text-white font-bold text-lg tracking-wide">CivicVerify</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-6 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'masyarakat')
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('report.create')" :active="request()->routeIs('report.create')">
                                Buat Laporan
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'konsultan')
                            <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.*')">
                                Dashboard Admin
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'surveyor')
                            <x-nav-link :href="route('surveyor.tasks')" :active="request()->routeIs('surveyor.*')">
                                Tugas Survei
                            </x-nav-link>
                        @endif

                        @if(auth()->user()->role === 'kementerian')
                            <x-nav-link :href="route('kementerian.index')" :active="request()->routeIs('kementerian.*')">
                                Monitoring
                            </x-nav-link>
                        @endif

                        <x-nav-link :href="route('public.index')" :active="request()->routeIs('public.*')">
                            Laporan Publik
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <!-- Role Badge -->
                    <span class="mr-3 text-xs font-semibold px-2.5 py-1 rounded-full
                        @if(auth()->user()->role === 'konsultan') bg-purple-200 text-purple-800
                        @elseif(auth()->user()->role === 'surveyor') bg-yellow-200 text-yellow-800
                        @elseif(auth()->user()->role === 'kementerian') bg-blue-200 text-blue-900
                        @else bg-green-200 text-green-800
                        @endif">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-3 py-2 text-sm text-white hover:text-blue-200 transition rounded-lg hover:bg-blue-600 focus:outline-none">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profil Saya') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger (mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-blue-200 hover:text-white hover:bg-blue-600 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-800">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'masyarakat')
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('report.create')" :active="request()->routeIs('report.create')">
                        Buat Laporan
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->role === 'konsultan')
                    <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.*')">
                        Dashboard Admin
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->role === 'surveyor')
                    <x-responsive-nav-link :href="route('surveyor.tasks')" :active="request()->routeIs('surveyor.*')">
                        Tugas Survei
                    </x-responsive-nav-link>
                @endif
                @if(auth()->user()->role === 'kementerian')
                    <x-responsive-nav-link :href="route('kementerian.index')" :active="request()->routeIs('kementerian.*')">
                        Monitoring
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('public.index')" :active="request()->routeIs('public.*')">
                    Laporan Publik
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-blue-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-blue-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Profil Saya
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        Logout
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
