<nav x-data="{ open: false }" class="bg-card border-b border-border sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-8 w-auto fill-current text-primary" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-3 sm:ms-8 sm:flex items-center h-full">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        {{ __('Pengajuan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.*')">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ __('Persetujuan') }}
                    </x-nav-link>
                    <x-nav-link :href="route('travel-report-approvals.index')" :active="request()->routeIs('travel-report-approvals.*')">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-4H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2h-1"/></svg>
                        {{ __('Approval LHP') }}
                    </x-nav-link>
                    <x-nav-link :href="route('travel-reports.index')" :active="request()->routeIs('travel-reports.*')">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        {{ __('LHP') }}
                    </x-nav-link>

                    @auth
                        @if(Auth::user()->hasPermission('reports.view'))
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ __('Laporan') }}
                            </x-nav-link>
                        @endif

                        @if(Auth::user()->hasPermission('settings.manage'))
                            <div class="relative h-full flex items-center" x-data="{ settingsOpen: false }" @click.outside="settingsOpen = false">
                                <button @click="settingsOpen = !settingsOpen"
                                    class="inline-flex items-center gap-1.5 px-1 h-full border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none
                                        {{ request()->routeIs('settings.*') ? 'border-primary text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border' }}">
                                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ __('Pengaturan') }}
                                    <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': settingsOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <!-- Dropdown Panel -->
                                <div x-show="settingsOpen"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute left-0 top-full mt-1 w-56 bg-popover rounded-lg shadow-lg border border-border py-1 z-50"
                                     style="display: none">

                                    <div class="px-3 py-2">
                                        <p class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">Manajemen</p>
                                    </div>
                                    @foreach([
                                        ['route' => 'settings.users.index', 'label' => 'Pengguna', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                                        ['route' => 'settings.divisions.index', 'label' => 'Divisi', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                                        ['route' => 'settings.roles.index', 'label' => 'Peran', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                                        ['route' => 'settings.permissions', 'label' => 'Hak Akses', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                                    ] as $item)
                                        <a href="{{ route($item['route']) }}"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm text-foreground hover:bg-accent hover:text-accent-foreground transition-colors
                                                {{ request()->routeIs(rtrim($item['route'], '.index').'*') ? 'bg-accent font-medium' : '' }}">
                                            <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                            </svg>
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach

                                    <div class="separator my-1"></div>

                                    <div class="px-3 py-2">
                                        <p class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wider">Konfigurasi</p>
                                    </div>
                                    @foreach([
                                        ['route' => 'settings.flows', 'label' => 'Alur Persetujuan', 'icon' => 'M4 6h16M4 12h8m-8 6h16'],
                                        ['route' => 'settings.policies.index', 'label' => 'Kebijakan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                        ['route' => 'settings.travel-zones.index', 'label' => 'Zona Perjalanan', 'icon' => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8'],
                                        ['route' => 'settings.client-codes.index', 'label' => 'Kode Klien', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z'],
                                        ['route' => 'settings.audit-logs.index', 'label' => 'Audit Log', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                                    ] as $item)
                                        <a href="{{ route($item['route']) }}"
                                            class="flex items-center gap-2.5 px-3 py-2 text-sm text-foreground hover:bg-accent hover:text-accent-foreground transition-colors
                                                {{ request()->routeIs(rtrim($item['route'], '.index').'*') ? 'bg-accent font-medium' : '' }}">
                                            <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                            </svg>
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
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
                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent transition-colors focus:outline-none">
                            <div class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
                                <span class="text-xs font-bold text-primary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                            <div class="text-left">
                                <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
                            </div>
                            <svg class="ms-1 h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2 border-b border-border">
                            <p class="text-xs font-medium text-foreground">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ __('Profil') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-muted-foreground hover:text-foreground hover:bg-accent focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-border bg-card">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                🏠 {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('requests.index')" :active="request()->routeIs('requests.*')">
                📄 {{ __('Pengajuan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('approvals.index')" :active="request()->routeIs('approvals.*')">
                ✅ {{ __('Persetujuan') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('travel-reports.index')" :active="request()->routeIs('travel-reports.*')">
                📋 {{ __('LHP') }}
            </x-responsive-nav-link>

            @auth
                @if(Auth::user()->hasPermission('reports.view'))
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                        📊 {{ __('Laporan') }}
                    </x-responsive-nav-link>
                @endif

                @if(Auth::user()->hasPermission('settings.manage'))
                    <div class="pt-2 pb-1 px-4 text-xs font-semibold text-muted-foreground uppercase tracking-wider">Pengaturan</div>
                    <x-responsive-nav-link :href="route('settings.users.index')" :active="request()->routeIs('settings.users.*')">
                        &ensp;👤 Pengguna
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.divisions.index')" :active="request()->routeIs('settings.divisions.*')">
                        &ensp;🏢 Divisi
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.roles.index')" :active="request()->routeIs('settings.roles.*')">
                        &ensp;🛡 Peran
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.permissions')" :active="request()->routeIs('settings.permissions')">
                        &ensp;🔑 Hak Akses
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.flows')" :active="request()->routeIs('settings.flows')">
                        &ensp;⚙ Alur Persetujuan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.policies.index')" :active="request()->routeIs('settings.policies.*')">
                        &ensp;📋 Kebijakan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.travel-zones.index')" :active="request()->routeIs('settings.travel-zones.*')">
                        &ensp;✈ Zona Perjalanan
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.client-codes.index')" :active="request()->routeIs('settings.client-codes.*')">
                        &ensp;🏷 Kode Klien
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('settings.audit-logs.index')" :active="request()->routeIs('settings.audit-logs.*')">
                        &ensp;📋 Audit Log
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Profile Options -->
        <div class="pt-4 pb-3 border-t border-border">
            <div class="flex items-center gap-3 px-4">
                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                    <span class="text-sm font-bold text-primary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                </div>
                <div>
                    <div class="font-medium text-sm text-foreground">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-muted-foreground">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Keluar') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>