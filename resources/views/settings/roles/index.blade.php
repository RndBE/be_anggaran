<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Manajemen Role</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Kelola role pengguna dalam sistem</p>
            </div>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-role'))"
                class="btn-default btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Role
            </button>
        </div>
    </x-slot>

    <div class="py-6" x-data="roleManager()" @open-create-role.window="openCreate()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->has('error'))
                <div class="alert-destructive">{{ $errors->first('error') }}</div>
            @endif

            <div class="card overflow-hidden">
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th>Nama Role</th>
                                <th>Slug</th>
                                <th class="text-center">Users</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td class="text-muted-foreground">{{ $loop->iteration }}</td>
                                    <td class="font-medium text-foreground">{{ $role->name }}</td>
                                    <td><code
                                            class="text-xs font-mono bg-primary/10 text-primary px-2 py-0.5 rounded">{{ $role->slug }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="{{ $role->users_count > 0 ? 'badge-info' : 'badge-secondary' }}">{{ $role->users_count }}</span>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                @click="openEdit({{ $role->id }}, '{{ addslashes($role->name) }}', '{{ $role->slug }}')"
                                                class="btn-outline btn-sm">Edit</button>
                                            <form method="POST" action="{{ route('settings.roles.destroy', $role) }}"
                                                onsubmit="return confirm('Hapus role \'{{ $role->name }}\'?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="btn-destructive btn-sm {{ $role->users_count > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                    {{ $role->users_count > 0 ? 'disabled' : '' }}>Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-sm text-muted-foreground">
                                        Belum ada role. <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('open-create-role'))"
                                            class="text-primary hover:underline">Buat yang pertama.</button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="modal === 'create'" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>
            <div class="relative card w-full max-w-md p-6 z-10" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title">Tambah Role Baru</h3>
                    <button @click="close()" class="text-muted-foreground hover:text-foreground"><svg class="w-5 h-5"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="separator mb-4"></div>
                <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-4">
                    @csrf
                    <div class="form-group">
                        <label class="label mb-1 block">Nama Role</label>
                        <input type="text" name="name" id="create_name" required autofocus class="input"
                            placeholder="e.g., Finance Manager" value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label class="label mb-1 block">Slug</label>
                        <input type="text" name="slug" id="create_slug" required class="input font-mono"
                            placeholder="e.g., finance-manager" value="{{ old('slug') }}">
                        <p class="text-xs text-muted-foreground mt-1">Huruf kecil, angka, dash. Dipakai di kode
                            otorisasi.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-border">
                        <button type="button" @click="close()" class="btn-outline">Batal</button>
                        <button type="submit" class="btn-default">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div x-show="modal === 'edit'" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="close()"></div>
            <div class="relative card w-full max-w-md p-6 z-10" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title">Edit Role</h3>
                    <button @click="close()" class="text-muted-foreground hover:text-foreground"><svg class="w-5 h-5"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="separator mb-4"></div>
                <form method="POST" :action="`/settings/roles/${editId}`" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="form-group">
                        <label class="label mb-1 block">Nama Role</label>
                        <input type="text" name="name" required class="input" x-model="editName">
                    </div>
                    <div class="form-group">
                        <label class="label mb-1 block">Slug</label>
                        <input type="text" name="slug" required class="input font-mono" x-model="editSlug">
                        <p class="text-xs text-amber-500 mt-1">⚠ Mengubah slug bisa mempengaruhi otorisasi.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t border-border">
                        <button type="button" @click="close()" class="btn-outline">Batal</button>
                        <button type="submit" class="btn-default">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function roleManager() {
            return {
                modal: null, editId: null, editName: '', editSlug: '',
                openCreate() { this.modal = 'create'; this.$nextTick(() => document.getElementById('create_name')?.focus()); },
                openEdit(id, name, slug) { this.editId = id; this.editName = name; this.editSlug = slug; this.modal = 'edit'; },
                close() { this.modal = null; },
            };
        }
        document.getElementById('create_name')?.addEventListener('input', function () {
            const slug = document.getElementById('create_slug');
            if (!slug.dataset.manual) slug.value = this.value.toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
        });
        document.getElementById('create_slug')?.addEventListener('input', function () {
            this.dataset.manual = 'true';
            this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        });
    </script>
</x-app-layout>