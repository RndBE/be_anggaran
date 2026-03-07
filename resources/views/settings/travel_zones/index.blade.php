<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Travel Zones & Meal Allowance</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Kelola zona perjalanan dinas dan uang makan</p>
            </div>
            <button onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))" class="btn-default btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Zone
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="text-center w-24">Zone</th>
                                <th>Nama</th>
                                <th class="text-right">Meal Allowance / Hari</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($zones as $zone)
                                <tr>
                                    <td class="text-center">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10 text-primary font-bold text-sm">{{ $zone->zone }}</span>
                                    </td>
                                    <td class="font-medium text-foreground">{{ $zone->name }}</td>
                                    <td class="text-right font-semibold text-primary">Rp
                                        {{ number_format($zone->meal_allowance, 0, ',', '.') }}</td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ $zone->id }}, zone: {{ $zone->zone }}, name: '{{ addslashes($zone->name) }}', meal_allowance: {{ $zone->meal_allowance }} } }))"
                                                class="btn-outline btn-sm">Edit</button>
                                            <form method="POST" action="{{ route('settings.travel-zones.destroy', $zone) }}"
                                                onsubmit="return confirm('Hapus Zone {{ $zone->zone }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-destructive btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-sm text-muted-foreground">
                                        Belum ada travel zone.
                                        <button onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                                            class="text-primary hover:underline">Tambah zone pertama.</button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div x-data="{ open: false }" @open-create-modal.window="open = true" x-show="open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative card w-full max-w-lg p-6 z-10" @click.stop
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title">Tambah Travel Zone Baru</h3>
                <button @click="open = false" class="text-muted-foreground hover:text-foreground"><svg class="w-5 h-5"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
            <div class="separator mb-4"></div>
            @if($errors->any() && !old('_edit_mode'))
                <div class="alert-destructive mb-4">
                    <ul class="list-disc pl-4 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <form method="POST" action="{{ route('settings.travel-zones.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="_edit_mode" value="0">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <x-input-label for="create_zone">Nomor Zone</x-input-label>
                        <x-text-input id="create_zone" name="zone" type="number" min="1" :value="old('zone', $nextZone)"
                            required autofocus />
                        <p class="text-xs text-muted-foreground mt-1">Harus unik.</p>
                    </div>
                    <div class="form-group">
                        <x-input-label for="create_name">Nama Zone</x-input-label>
                        <x-text-input id="create_name" name="name" type="text" :value="old('name')" required
                            placeholder="e.g., Jabodetabek" />
                    </div>
                </div>
                <div class="form-group">
                    <x-input-label for="create_meal">Meal Allowance / Hari (Rp)</x-input-label>
                    <div class="relative mt-1">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">Rp</span>
                        <x-text-input id="create_meal" name="meal_allowance" type="number" step="1000" min="0"
                            class="pl-10" :value="old('meal_allowance')" required placeholder="0" />
                    </div>
                </div>
                <div class="pt-3 border-t border-border flex justify-end gap-3">
                    <button type="button" @click="open = false" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-default">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-data="{ open: false, id: null, zone: '', name: '', meal_allowance: 0, get actionUrl() { return '/settings/travel-zones/' + this.id; } }"
        @open-edit-modal.window="open = true; id = $event.detail.id; zone = $event.detail.zone; name = $event.detail.name; meal_allowance = $event.detail.meal_allowance;"
        x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative card w-full max-w-lg p-6 z-10" @click.stop
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title">Edit Zone — <span x-text="zone"></span></h3>
                <button @click="open = false" class="text-muted-foreground hover:text-foreground"><svg class="w-5 h-5"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg></button>
            </div>
            <div class="separator mb-4"></div>
            @if($errors->any() && old('_edit_mode'))
                <div class="alert-destructive mb-4">
                    <ul class="list-disc pl-4 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            <form method="POST" :action="actionUrl" class="space-y-4">
                @csrf @method('PUT')
                <input type="hidden" name="_edit_mode" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <x-input-label for="edit_zone">Nomor Zone</x-input-label>
                        <x-text-input id="edit_zone" name="zone" type="number" min="1" x-model="zone" required />
                    </div>
                    <div class="form-group">
                        <x-input-label for="edit_name">Nama Zone</x-input-label>
                        <x-text-input id="edit_name" name="name" type="text" x-model="name" required />
                    </div>
                </div>
                <div class="form-group">
                    <x-input-label for="edit_meal">Meal Allowance / Hari (Rp)</x-input-label>
                    <div class="relative mt-1">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground font-medium">Rp</span>
                        <x-text-input id="edit_meal" name="meal_allowance" type="number" step="1000" min="0"
                            class="pl-10" x-model="meal_allowance" required />
                    </div>
                </div>
                <div class="pt-3 border-t border-border flex justify-end gap-3">
                    <button type="button" @click="open = false" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-default">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any() && !old('_edit_mode'))
        <script>window.addEventListener('load', () => window.dispatchEvent(new CustomEvent('open-create-modal')));</script>
    @endif
    @if($errors->any() && old('_edit_mode'))
        <script>window.addEventListener('load', () => window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ old('_edit_id', 0) }}, zone: {{ old('zone', 0) }}, name: '{{ addslashes(old('name', '')) }}', meal_allowance: {{ old('meal_allowance', 0) }} } })));</script>
    @endif
</x-app-layout>