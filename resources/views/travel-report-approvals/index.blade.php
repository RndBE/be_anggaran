<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">Approval LHP</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Laporan Hasil Perjalanan yang menunggu persetujuan Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            {{-- PENDING --}}
            <div class="card">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Menunggu Persetujuan Anda</h3>
                </div>
                @if($pending->isEmpty())
                    <div class="p-8 text-center text-muted-foreground">
                        <svg class="w-10 h-10 mx-auto mb-2 text-muted" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm">Tidak ada LHP yang perlu disetujui saat ini.</p>
                    </div>
                @else
                    <div class="divide-y divide-border">
                        @foreach($pending as $approval)
                            <div class="p-5 flex items-center justify-between hover:bg-muted/30 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="badge badge-warning text-xs">{{ $approval->step_label }}</span>
                                        <span class="text-xs text-muted-foreground">LHP
                                            #{{ $approval->travel_report_id }}</span>
                                    </div>
                                    <p class="font-medium text-foreground truncate">
                                        {{ $approval->travelReport->destination_city }}
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        Oleh: <strong>{{ $approval->travelReport->user->name }}</strong>
                                        · {{ $approval->travelReport->departure_date->format('d M Y') }}
                                        – {{ $approval->travelReport->return_date->format('d M Y') }}
                                    </p>
                                </div>
                                <a href="{{ route('travel-report-approvals.show', $approval) }}"
                                    class="btn-default btn-sm ml-4 shrink-0">
                                    Review →
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- PROCESSED --}}
            @if($processed->isNotEmpty())
                <div class="card">
                    <div class="p-6 border-b border-border">
                        <h3 class="card-title">✅ Riwayat Approval Saya</h3>
                    </div>
                    <div class="divide-y divide-border">
                        @foreach($processed as $approval)
                            <div class="p-4 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-foreground">
                                        {{ $approval->travelReport->destination_city }}
                                        <span class="text-xs text-muted-foreground ml-2">{{ $approval->step_label }}</span>
                                    </p>
                                    <p class="text-xs text-muted-foreground">Oleh: {{ $approval->travelReport->user->name }}</p>
                                </div>
                                <span @class([
                                    'badge text-xs',
                                    'badge-success' => $approval->status === 'approved',
                                    'badge-destructive' => $approval->status === 'rejected',
                                ])>{{ $approval->status === 'approved' ? 'Disetujui' : 'Ditolak' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>