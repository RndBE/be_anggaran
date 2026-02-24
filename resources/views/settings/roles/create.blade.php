<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Role Baru
            </h2>
            <a href="{{ route('settings.roles.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Kembali ke Daftar Role
            </a>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 sm:px-8 pt-2 pb-4">
                <form method="POST" action="{{ route('settings.roles.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Role')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" placeholder="e.g., Head of Department" required autofocus />
                        <p class="mt-1 text-xs text-gray-400">Nama tampilan yang mudah dibaca manusia.</p>
                    </div>

                    <div>
                        <x-input-label for="slug" :value="__('Slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                            value="{{ old('slug') }}" placeholder="e.g., head-of-dept" required pattern="[a-z0-9_-]+"
                            title="Hanya huruf kecil, angka, dash, underscore" />
                        <p class="mt-1 text-xs text-gray-400">Huruf kecil, angka, dash (<code>-</code>), underscore
                            (<code>_</code>) saja. Dipakai di middleware.</p>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <a href="{{ route('settings.roles.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                            Simpan Role
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function () {
            const slugField = document.getElementById('slug');
            // Only auto-fill if user hasn't manually edited slug
            if (!slugField.dataset.manualEdit) {
                slugField.value = this.value
                    .toLowerCase()
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9_-]/g, '');
            }
        });
        document.getElementById('slug').addEventListener('input', function () {
            this.dataset.manualEdit = 'true';
            // Sanitize in real-time
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '');
        });
    </script>
</x-app-layout>