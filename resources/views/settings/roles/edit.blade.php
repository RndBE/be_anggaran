<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Role: <span class="font-normal text-indigo-600">{{ $role->name }}</span>
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

            <div class="bg-white shadow sm:rounded-lg p-6 sm:px-8 py-4">
                <form method="POST" action="{{ route('settings.roles.update', $role) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" :value="__('Nama Role')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $role->name) }}" required autofocus />
                    </div>

                    <div>
                        <x-input-label for="slug" :value="__('Slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full font-mono"
                            value="{{ old('slug', $role->slug) }}" required pattern="[a-z0-9_-]+"
                            title="Hanya huruf kecil, angka, dash, underscore" />
                        <p class="mt-1 text-xs text-amber-600">
                            ⚠️ Mengubah slug akan mempengaruhi semua middleware dan pengecekan role di kode.
                        </p>
                    </div>

                    {{-- Info users --}}
                    @php $userCount = $role->users()->count(); @endphp
                    @if($userCount > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-700">
                            Role ini digunakan oleh <strong>{{ $userCount }} user</strong>. Perubahan akan langsung berlaku.
                        </div>
                    @endif

                    <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                        <a href="{{ route('settings.roles.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                            Update Role
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('slug').addEventListener('input', function () {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_-]/g, '');
        });
    </script>
</x-app-layout>