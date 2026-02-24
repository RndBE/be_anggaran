<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User Baru</h2>
            <a href="{{ route('settings.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 sm:p-8">
                <form method="POST" action="{{ route('settings.users.store') }}"
                    class="space-y-5" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Nama Lengkap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name') }}" required autofocus />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                            value="{{ old('email') }}" required />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <p class="mt-1 text-xs text-gray-400">Min. 8 karakter.</p>
                    </div>

                    {{-- Division & Level --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="division_id" :value="__('Divisi')" />
                            <select id="division_id" name="division_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">— Tidak ada divisi —</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="level" :value="__('Level Jabatan')" />
                            <select id="level" name="level"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                <option value="">— Tidak ada —</option>
                                @foreach([4 => 'Level 4 (Staff/Pengaju)', 3 => 'Level 3 (Supervisor)', 2 => 'Level 2 (Manager)', 1 => 'Level 1 (Director)'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('level') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Signature --}}
                    <div>
                        <x-input-label for="signature_create" :value="__('Tanda Tangan')" />
                        <div class="flex items-center gap-3 mt-1 border border-dashed border-gray-300 rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer"
                            onclick="document.getElementById('signature_create').click()">
                            <svg class="w-7 h-7 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            <span class="text-sm text-gray-500" id="sig_create_label">Pilih file tanda tangan…</span>
                        </div>
                        <input type="file" id="signature_create" name="signature"
                            accept=".jpg,.jpeg,.png,.gif,.svg"
                            class="hidden"
                            onchange="document.getElementById('sig_create_label').textContent = this.files[0]?.name ?? 'Pilih file tanda tangan…'">
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG, SVG · Maks 2 MB · Opsional.</p>
                        @error('signature')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Assign Roles --}}
                    <div>
                        <x-input-label :value="__('Assign Roles')" />
                        <div class="mt-2 space-y-2">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-2.5 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                        <code class="ml-2 text-xs text-indigo-500 font-mono bg-indigo-50 px-1 rounded">{{ $role->slug }}</code>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex justify-between">
                        <a href="{{ route('settings.users.index') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
