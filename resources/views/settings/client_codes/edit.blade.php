<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Client Code — {{ $clientCode->prefix }}
            </h2>
            <a href="{{ route('settings.client-codes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">←
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

            <div class="bg-white shadow sm:rounded-lg p-6 sm:px-8 py-4">
                <form method="POST" action="{{ route('settings.client-codes.update', $clientCode) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="prefix" :value="__('Prefix')" />
                            <x-text-input id="prefix" name="prefix" type="text" class="mt-1 block w-full"
                                value="{{ old('prefix', $clientCode->prefix) }}" required autofocus />
                            <p class="mt-1 text-xs text-gray-400">Harus unik. Huruf kapital disarankan.</p>
                        </div>
                        <div>
                            <x-input-label for="instansi_singkat" :value="__('Instansi Singkat')" />
                            <x-text-input id="instansi_singkat" name="instansi_singkat" type="text"
                                class="mt-1 block w-full"
                                value="{{ old('instansi_singkat', $clientCode->instansi_singkat) }}" required />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="counter" :value="__('Counter (Nomor Urut Saat Ini)')" />
                        <x-text-input id="counter" name="counter" type="number" min="0" class="mt-1 block w-full"
                            value="{{ old('counter', $clientCode->counter) }}" required />
                        <p class="mt-1 text-xs text-gray-400">Ubah hanya jika perlu mereset atau koreksi nomor urut.</p>
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Nama Klien (Opsional — Sensitif)')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $clientCode->name) }}" placeholder="Nama lengkap instansi klien" />
                    </div>

                    <div class="pt-3 border-t flex justify-between">
                        <a href="{{ route('settings.client-codes.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 bg-white border rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>