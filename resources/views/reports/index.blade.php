<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">{{ __('Laporan & Log Audit') }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Ringkasan semua pengajuan dalam sistem</p>
            </div>
            <a href="{{ route('reports.export') }}" class="btn-secondary btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor CSV
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Ringkasan Semua Pengajuan</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pemohon</th>
                                <th>Kode Klien</th>
                                <th>Tipe</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                                <tr>
                                    <td class="font-mono text-xs text-muted-foreground">REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>
                                    <td class="font-medium text-foreground">{{ $req->user->name }}</td>
                                    <td class="text-sm text-muted-foreground">
                                        {{ $req->clientCode ? $req->clientCode->prefix . '-' . $req->clientCode->instansi_singkat : '-' }}
                                    </td>
                                    <td class="capitalize text-sm text-muted-foreground">{{ $req->type }}</td>
                                    <td class="font-bold text-foreground whitespace-nowrap">Rp {{ number_format($req->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($req->status) {
                                                'approved', 'paid' => 'badge-success',
                                                'rejected'         => 'badge-destructive',
                                                'revision_requested' => 'badge-purple',
                                                'pending'          => 'badge-warning',
                                                default            => 'badge-secondary',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $req->status)) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('reports.show', $req) }}" class="btn-outline btn-sm">Detail</a>
                                            @if(Auth::user()->hasPermission('requests.delete'))
                                                <form id="delete-form-{{ $req->id }}" method="POST" action="{{ route('requests.destroy', $req) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        onclick="openDeleteModal({{ $req->id }}, '{{ addslashes($req->title) }}')"
                                                        class="btn-destructive btn-sm">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-muted-foreground">Belum ada pengajuan dalam sistem.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="relative card w-full max-w-md mx-4 p-6 animate-fade-in">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-destructive/10">
                    <svg class="w-5 h-5 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="card-title">Hapus Pengajuan</h3>
                    <p class="text-sm text-muted-foreground mt-1">
                        Apakah Anda yakin ingin menghapus <span id="deleteModalTitle" class="font-semibold text-foreground"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button onclick="closeDeleteModal()" class="btn-outline">Batal</button>
                <button id="confirmDeleteBtn" onclick="submitDelete()" class="btn-destructive">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        let pendingDeleteId = null;
        function openDeleteModal(id, title) {
            pendingDeleteId = id;
            document.getElementById('deleteModalTitle').textContent = '"' + title + '"';
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            pendingDeleteId = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }
        function submitDelete() {
            if (pendingDeleteId) document.getElementById('delete-form-' + pendingDeleteId).submit();
        }
    </script>
</x-app-layout>