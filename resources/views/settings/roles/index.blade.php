<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Role</h2>
            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-role'))"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Role
            </button>
        </div>
    </x-slot>

    <div class="py-5" x-data="roleManager()" @open-create-role.window="openCreate()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->has('error'))
                <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first('error') }}</div>
            @endif

            {{-- Table --}}
            <div class="bg-white shadow sm:rounded-lg border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs w-10">#</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Nama Role</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Slug</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider text-xs">Users</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-gray-400">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $role->name }}</td>
                                <td class="px-6 py-4">
                                    <code class="text-xs font-mono bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded">{{ $role->slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $role->users_count > 0 ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $role->users_count }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button"
                                            @click="openEdit({{ $role->id }}, '{{ addslashes($role->name) }}', '{{ $role->slug }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('settings.roles.destroy', $role) }}"
                                            onsubmit="return confirm('Hapus role \'{{ $role->name }}\'? Ini tidak bisa di-undo.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors
                                                    {{ $role->users_count > 0 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                    Belum ada role.
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-create-role'))"
                                        class="text-indigo-600 hover:underline">Buat yang pertama.</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── CREATE MODAL ─────────────────────────────────────────────── --}}
        <div x-show="modal === 'create'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="close()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Tambah Role Baru</h3>
                    <button @click="close()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                        <input type="text" name="name" id="create_name" required autofocus
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                            placeholder="e.g., Finance Manager" value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" id="create_slug" required
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm font-mono"
                            placeholder="e.g., finance-manager" value="{{ old('slug') }}">
                        <p class="mt-1 text-xs text-gray-400">Huruf kecil, angka, dash. Dipakai di kode otorisasi.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="close()"
                            class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── EDIT MODAL ───────────────────────────────────────────────── --}}
        <div x-show="modal === 'edit'" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display:none">
            <div class="absolute inset-0 bg-black/40" @click="close()"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Edit Role</h3>
                    <button @click="close()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="`/settings/roles/${editId}`" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Role</label>
                        <input type="text" name="name" required
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                            x-model="editName">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" required
                            class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm font-mono"
                            x-model="editSlug">
                        <p class="mt-1 text-xs text-amber-500">⚠ Mengubah slug bisa mempengaruhi otorisasi yang bergantung pada slug ini.</p>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="close()"
                            class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function roleManager() {
            return {
                modal: null,
                editId: null,
                editName: '',
                editSlug: '',

                openCreate() {
                    this.modal = 'create';
                    this.$nextTick(() => document.getElementById('create_name')?.focus());
                },

                openEdit(id, name, slug) {
                    this.editId   = id;
                    this.editName = name;
                    this.editSlug = slug;
                    this.modal    = 'edit';
                },

                close() { this.modal = null; },
            };
        }

        // Auto-slug from name on create form
        document.getElementById('create_name')?.addEventListener('input', function () {
            const slug = document.getElementById('create_slug');
            if (!slug.dataset.manual) {
                slug.value = this.value.toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
            }
        });
        document.getElementById('create_slug')?.addEventListener('input', function () {
            this.dataset.manual = 'true';
            this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        });
    </script>
</x-app-layout>
