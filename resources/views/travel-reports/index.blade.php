<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Laporan Hasil Perjalanan</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Daftar Laporan Hasil Perjalanan Dinas Luar Kota</p>
            </div>
            <a href="{{ route('travel-reports.create') }}" class="btn-default btn-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat LHP
            </a>
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
                                <th>Kota Tujuan</th>
                                <th>Pembuat</th>
                                <th>Divisi</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                                <tr>
                                    <td class="text-sm text-muted-foreground whitespace-nowrap">
                                        {{ $report->departure_date->format('d M') }} —
                                        {{ $report->return_date->format('d M Y') }}
                                    </td>
                                    <td class="font-medium text-foreground">{{ $report->destination_city }}</td>
                                    <td class="text-sm text-foreground">{{ $report->user->name }}</td>
                                    <td class="text-sm text-muted-foreground">{{ $report->user->division?->name ?? '—' }}
                                    </td>
                                    <td class="text-sm text-muted-foreground whitespace-nowrap">
                                        {{ $report->departure_date->diffInDays($report->return_date) + 1 }} hari
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($report->status) {
                                                'submitted' => 'badge-info',
                                                'approved' => 'badge-success',
                                                'draft' => 'badge-secondary',
                                                default => 'badge-secondary',
                                            };
                                        @endphp
                                        <span class="{{ $badgeClass }}">{{ ucfirst($report->status) }}</span>
                                    </td>
                                    <td class="whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('travel-reports.show', $report) }}"
                                                class="btn-outline btn-sm">Detail</a>
                                            <a href="{{ route('travel-reports.print', $report) }}" target="_blank"
                                                class="btn-secondary btn-sm">
                                                🖨️ Cetak
                                            </a>
                                            @if($report->user_id === Auth::id() || Auth::user()->hasPermission('requests.view-all'))
                                                <form method="POST" action="{{ route('travel-reports.destroy', $report) }}"
                                                    class="inline" onsubmit="return confirm('Yakin ingin menghapus LHP ini?')">
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
                                    <td colspan="7" class="py-12 text-center">
                                        <svg class="w-10 h-10 mx-auto mb-3 text-muted" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-muted-foreground">Belum ada LHP.
                                            <a href="{{ route('travel-reports.create') }}"
                                                class="text-primary hover:underline">Buat LHP baru →</a>
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages())
                    <div class="px-6 py-3 border-t border-border">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>