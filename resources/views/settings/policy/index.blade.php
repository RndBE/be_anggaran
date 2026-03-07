<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reimbursement Policies</h2>
            <a href="{{ route('settings.policies.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Policy
            </a>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->has('error'))
                <div class="p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Nama
                            </th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Key
                            </th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">
                                Deskripsi</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Nilai
                                (Rp)</th>
                            <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($policies as $policy)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $policy->name }}</td>
                                <td class="px-6 py-4">
                                    <code
                                        class="text-xs font-mono bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded">{{ $policy->key }}</code>
                                </td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $policy->description ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-blue-700">
                                    Rp {{ number_format($policy->value, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('settings.policies.edit', $policy) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('settings.policies.destroy', $policy) }}"
                                            onsubmit="return confirm('Hapus policy \'{{ $policy->name }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                    Belum ada policy. <a href="{{ route('settings.policies.create') }}"
                                        class="text-indigo-600 hover:underline">Tambah policy pertama.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>