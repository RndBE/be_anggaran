<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">{{ __('Pengajuan Saya') }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Daftar pengajuan anggaran dan reimbursement</p>
            </div>
            @auth
                @if(!Auth::user()->hasPermission('requests.view-all'))
                    <a href="{{ route('requests.create') }}" class="btn-default btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Pengajuan Baru
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr>
                                    <td class="text-sm text-muted-foreground whitespace-nowrap">
                                        {{ $req->created_at->format('d M Y') }}
                                    </td>
                                    <td class="font-medium text-foreground">{{ $req->title }}</td>
                                    <td>
                                        <span class="{{ $req->type == 'budget' ? 'badge-info' : 'badge-purple' }}">
                                            {{ ucfirst($req->type) }}
                                        </span>
                                    </td>
                                    <td class="font-bold text-foreground whitespace-nowrap">
                                        Rp {{ number_format($req->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($req->status) {
                                                'approved', 'paid' => 'badge-success',
                                                'rejected' => 'badge-destructive',
                                                'revision_requested' => 'badge-purple',
                                                'pending' => 'badge-warning',
                                                default => 'badge-secondary',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('requests.show', $req) }}"
                                                class="btn-outline btn-sm">Lihat</a>
                                            @if($req->status === 'draft' || $req->status === 'revision_requested')
                                                <a href="{{ route('requests.edit', $req) }}"
                                                    class="btn-secondary btn-sm">Ubah</a>
                                            @endif
                                            @php
                                                $deletableStatuses = ['draft', 'submitted', 'revision_requested'];
                                                $canDelete = Auth::user()->hasPermission('requests.delete')
                                                    || ($req->user_id === Auth::id() && in_array($req->status, $deletableStatuses));
                                            @endphp
                                            @if($canDelete)
                                                <form method="POST" action="{{ route('requests.destroy', $req) }}"
                                                    class="inline" onsubmit="return confirm('Yakin ingin menghapus request \" {{ addslashes($req->title) }}\"?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-destructive btn-sm">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-muted" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-muted-foreground">Belum ada pengajuan.
                                            <a href="{{ route('requests.create') }}"
                                                class="text-primary hover:underline">Buat sekarang →</a>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>