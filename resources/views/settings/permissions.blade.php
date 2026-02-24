<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings: Manajemen Permission') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Permission &amp; Role Matrix</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Centang role mana saja yang mendapatkan akses ke setiap permission. Perubahan langsung disimpan
                        ke database.
                    </p>

                    <form method="POST" action="{{ route('settings.permissions.update') }}">
                        @csrf

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-5 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider w-1/3">
                                            Permission
                                        </th>
                                        @foreach($roles as $role)
                                            <th
                                                class="px-3 py-3 text-center font-semibold text-gray-600 uppercase tracking-wider">
                                                {{ $role->name }}
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @foreach($permissions as $permission)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="font-medium text-gray-900">{{ $permission->name }}</div>
                                                @if($permission->description)
                                                    <div class="text-xs text-gray-400 mt-0.5">{{ $permission->description }}
                                                    </div>
                                                @endif
                                                <code
                                                    class="text-xs text-indigo-500 font-mono bg-indigo-50 px-1.5 py-0.5 rounded mt-1 inline-block">
                                                                    {{ $permission->slug }}
                                                                </code>
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="px-3 py-4 text-center">
                                                    <input type="checkbox" name="perm_{{ $permission->id }}[]"
                                                        value="{{ $role->id }}"
                                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                                                        @if($permission->roles->contains($role->id)) checked @endif>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-4 border-t border-gray-100 pt-4">
                            <p class="text-xs text-gray-400">
                                Perubahan akan segera berlaku untuk semua user yang memiliki role tersebut.
                            </p>
                            <button type="submit"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>