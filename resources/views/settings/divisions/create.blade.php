<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Divisi Baru</h2>
            <a href="{{ route('settings.divisions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">←
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 sm:p-8">
                <form method="POST" action="{{ route('settings.divisions.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Nama Divisi')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" required autofocus placeholder="e.g., Human Resources" />
                    </div>
                    <div>
                        <x-input-label for="slug" :value="__('Slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                            value="{{ old('slug') }}" required placeholder="e.g., hr" />
                        <p class="mt-1 text-xs text-gray-400">Huruf kecil, angka, dash, underscore.</p>
                    </div>
                    <div class="pt-3 border-t flex justify-between">
                        <a href="{{ route('settings.divisions.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 bg-white border rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('name').addEventListener('input', function () {
            const slug = document.getElementById('slug');
            if (!slug.dataset.manual) {
                slug.value = this.value.toLowerCase().trim().replace(/\s+/g, '-').replace(/[^a-z0-9_-]/g, '');
            }
        });
        document.getElementById('slug').addEventListener('input', function () {
            this.dataset.manual = 'true';
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '');
        });
    </script>
</x-app-layout>