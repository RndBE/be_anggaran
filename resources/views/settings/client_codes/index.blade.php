<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Client Codes</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Kelola kode klien untuk tagging pengajuan</p>
            </div>
            <button onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))" class="btn-default btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Client Code
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
                                <th>Prefix</th>
                                <th>Instansi Singkat</th>
                                <th class="text-center w-24">Counter</th>
                                <th>Nama Klien</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientCodes as $cc)
                                <tr>
                                    <td><span class="badge-info font-mono tracking-wide">{{ $cc->prefix }}</span></td>
                                    <td class="font-medium text-foreground">{{ $cc->instansi_singkat }}</td>
                                    <td class="text-center text-muted-foreground">{{ $cc->counter }}</td>
                                    <td class="text-muted-foreground italic">{{ $cc->name ?? '—' }}</td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ $cc->id }}, prefix: '{{ addslashes($cc->prefix) }}', instansi_singkat: '{{ addslashes($cc->instansi_singkat) }}', counter: {{ $cc->counter }}, name: '{{ addslashes($cc->name ?? '') }}' } }))"
                                                class="btn-outline btn-sm">Edit</button>
                                            <form method="POST" action="{{ route('settings.client-codes.destroy', $cc) }}"
                                                onsubmit="return confirm('Hapus client code {{ $cc->prefix }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-destructive btn-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-sm text-muted-foreground">
                                        Belum ada client code.
                                        <button onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                                            class="text-primary hover:underline">Tambah yang pertama.</button>
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
                <h3 class="card-title">Tambah Client Code</h3>
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
            <form method="POST" action="{{ route('settings.client-codes.store') }}" class="space-y-4">
                @csrf <input type="hidden" name="_edit_mode" value="0">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <x-input-label for="create_prefix">Prefix</x-input-label>
                        <x-text-input id="create_prefix" name="prefix" type="text" :value="old('prefix')" required
                            placeholder="e.g. GOV1" />
                        <p class="text-xs text-muted-foreground mt-1">Harus unik.</p>
                    </div>
                    <div class="form-group">
                        <x-input-label for="create_instansi">Instansi Singkat</x-input-label>
                        <x-text-input id="create_instansi" name="instansi_singkat" type="text"
                            :value="old('instansi_singkat')" required placeholder="e.g. BBWS" />
                    </div>
                </div>
                <div class="form-group">
                    <x-input-label for="create_counter">Counter</x-input-label>
                    <x-text-input id="create_counter" name="counter" type="number" min="0" :value="old('counter', 0)"
                        required />
                </div>
                <div class="form-group">
                    <x-input-label for="create_name">Nama Klien (Opsional)</x-input-label>
                    <x-text-input id="create_name" name="name" type="text" :value="old('name')"
                        placeholder="Nama lengkap instansi klien" />
                </div>
                <div class="pt-3 border-t border-border flex justify-end gap-3">
                    <button type="button" @click="open = false" class="btn-outline">Batal</button>
                    <button type="submit" class="btn-default">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-data="{ open: false, id: null, prefix: '', instansi_singkat: '', counter: 0, name: '', get actionUrl() { return '/settings/client-codes/' + this.id; } }"
        @open-edit-modal.window="open = true; id = $event.detail.id; prefix = $event.detail.prefix; instansi_singkat = $event.detail.instansi_singkat; counter = $event.detail.counter; name = $event.detail.name;"
        x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative card w-full max-w-lg p-6 z-10" @click.stop
            x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title">Edit Client Code — <span x-text="prefix" class="text-primary"></span></h3>
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
                @csrf @method('PUT') <input type="hidden" name="_edit_mode" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <x-input-label for="edit_prefix">Prefix</x-input-label>
                        <x-text-input id="edit_prefix" name="prefix" type="text" x-model="prefix" required />
                    </div>
                    <div class="form-group">
                        <x-input-label for="edit_instansi">Instansi Singkat</x-input-label>
                        <x-text-input id="edit_instansi" name="instansi_singkat" type="text" x-model="instansi_singkat"
                            required />
                    </div>
                </div>
                <div class="form-group">
                    <x-input-label for="edit_counter">Counter</x-input-label>
                    <x-text-input id="edit_counter" name="counter" type="number" min="0" x-model="counter" required />
                </div>
                <div class="form-group">
                    <x-input-label for="edit_name">Nama Klien (Opsional)</x-input-label>
                    <x-text-input id="edit_name" name="name" type="text" x-model="name"
                        placeholder="Nama lengkap instansi klien" />
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
        <script>window.addEventListener('load', () => window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { id: {{ old('_edit_id', 0) }}, prefix: '{{ addslashes(old('prefix', '')) }}', instansi_singkat: '{{ addslashes(old('instansi_singkat', '')) }}', counter: {{ old('counter', 0) }}, name: '{{ addslashes(old('name', '')) }}' } })));</script>
    @endif
</x-app-layout>