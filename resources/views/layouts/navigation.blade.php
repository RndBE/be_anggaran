<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-5 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                        {{ __('Requests') }}
                    </x-nav-link>
                    <x-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.*')">
                        {{ __('Approvals') }}
                    </x-nav-link>

                    @auth
                        @if(Auth::user()->hasPermission('reports.view'))
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                {{ __('Reports') }}
                            </x-nav-link>
                        @endif

                        {{-- Settings Dropdown --}}
                        @if(Auth::user()->hasPermission('settings.manage'))
                            <div class="relative" x-data="{ settingsOpen: false }" @click.outside="settingsOpen = false">
                                <button @click="settingsOpen = !settingsOpen"
                                    class="inline-flex items-center gap-1 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                                                                                                {{ request()->routeIs('settings.*') ? 'text-gray-900 border-b-2 border-indigo-400' : 'text-gray-500 hover:text-gray-700 border-b-2 border-transparent' }}">
                                    {{ __('Settings') }}
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                        :class="{ 'rotate-180': settingsOpen }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Dropdown Panel -->
                                <div x-show="settingsOpen" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute left-0 top-full mt-1 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50"
                                    style="display: none">

                                    {{-- User Management --}}
                                    <div class="px-3 py-1.5">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Manajemen</p>
                                    </div>
                                    <a href="{{ route('settings.users.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Users
                                    </a>
                                    <a href="{{ route('settings.divisions.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.divisions.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        Divisions
                                    </a>
                                    <a href="{{ route('settings.roles.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.roles.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        Roles
                                    </a>
                                    <a href="{{ route('settings.permissions') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.permissions') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                        Permissions
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    {{-- Workflow Config --}}
                                    <div class="px-3 py-1.5">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Konfigurasi</p>
                                    </div>
                                    <a href="{{ route('settings.flows') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.flows') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h8m-8 6h16" />
                                        </svg>
                                        Flow Builder
                                    </a>
                                    <a href="{{ route('settings.policies.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.policies.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        Policies
                                    </a>
                                    <a href="{{ route('settings.travel-zones.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                                                                                    {{ request()->routeIs('settings.travel-zones.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Travel Zones
                                    </a>
                                    <a href="{{ route('settings.whatsapp') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors
                                                       {{ request()->routeIs('settings.whatsapp*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : '' }}">
                                        <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                        </svg>
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Profile Dropdown (top right) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                {{ __('Requests') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.*')">
                {{ __('Approvals') }}
            </x-responsive-nav-link>

            @auth
                @if(Auth::user()->hasPermission('reports.view'))
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        {{ __('Reports') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasPermission('settings.manage'))
                    <div class="pt-2 pb-1 px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Settings</div>
                    <x-responsive-nav-link :href="route('settings.users.index')"
                        :active="request()->routeIs('settings.users.*')">
                        &ensp;👤 Users
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.divisions.index')"
                        :active="request()->routeIs('settings.divisions.*')">
                        &ensp;🏢 Divisions
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.roles.index')"
                        :active="request()->routeIs('settings.roles.*')">
                        &ensp;🛡 Roles
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.permissions')"
                        :active="request()->routeIs('settings.permissions')">
                        &ensp;🔑 Permissions
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.flows')" :active="request()->routeIs('settings.flows')">
                        &ensp;⚙ Flow Builder
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.policies.index')"
                        :active="request()->routeIs('settings.policies.*')">
                        &ensp;📋 Policies
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.travel-zones.index')"
                        :active="request()->routeIs('settings.travel-zones.*')">
                        &ensp;✈ Travel Zones
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Profile Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>