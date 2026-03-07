<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}"
                    class="text-muted-foreground hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-foreground">Detail Report</h2>
                    <p class="text-sm text-muted-foreground font-mono">
                        REQ-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Header Info Card --}}
            <div class="card p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Status</p>
                            <div class="mt-1">
                                @php
                                    $badgeClass = match ($report->status) {
                                        'approved', 'paid' => 'badge-success',
                                        'rejected' => 'badge-destructive',
                                        'revision_requested', 'revision' => 'badge-purple',
                                        'pending' => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ strtoupper($report->status) }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Pemohon</p>
                            <p class="text-sm font-medium text-foreground mt-1">{{ $report->user->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $report->user->division?->name ?? '—' }} · Level
                                {{ $report->user->level ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal
                                Pengajuan</p>
                            <p class="text-sm text-foreground mt-1">{{ $report->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Judul</p>
                            <p class="text-sm font-semibold text-foreground mt-1">{{ $report->title }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tipe</p>
                            <span class="badge-secondary mt-1 capitalize">{{ $report->type }}</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Client Code
                            </p>
                            <p class="text-sm text-foreground mt-1">
                                {{ $report->clientCode ? $report->clientCode->prefix . '-' . $report->clientCode->instansi_singkat : '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col justify-between">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total
                                Anggaran</p>
                            <p class="text-3xl font-bold text-primary mt-1">Rp
                                {{ number_format($report->total_amount, 0, ',', '.') }}</p>
                        </div>
                        @if($report->description)
                            <div class="mt-3">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Deskripsi
                                </p>
                                <p class="text-sm text-foreground mt-1">{{ $report->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Peserta Dinas --}}
            @if($report->participants->isNotEmpty())
                <div class="card px-6 py-4">
                    <h3 class="card-title mb-3">👥 Peserta Dinas</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($report->participants as $p)
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-primary/5 border border-primary/10 text-primary font-medium text-sm rounded-full">
                                <span
                                    class="w-5 h-5 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($p->name, 0, 1)) }}
                                </span>
                                {{ $p->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Approval Progress --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Progress Approval</h3>
                    <p class="card-description mt-0.5">Riwayat perjalanan approval</p>
                </div>
                <div class="p-6">
                    @if($allSteps->isEmpty())
                        <p class="text-sm text-muted-foreground italic text-center py-4">Belum ada flow approval.</p>
                    @else
                        <div class="relative">
                            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-border"></div>
                            <div class="space-y-0">
                                @foreach($allSteps as $step)
                                    @php
                                        $approval = $approvalsByStep->get($step->id);
                                        $isApproved = $approval && $approval->status === 'approved';
                                        $isPending = $approval && $approval->status === 'pending';
                                        $isRejected = $approval && $approval->status === 'rejected';
                                        $isRevision = $approval && $approval->status === 'revision';
                                        $isDone = $isApproved || $isRejected || $isRevision;

                                        if ($isApproved) {
                                            $dotClass = 'bg-green-500 ring-green-100';
                                            $cardClass = 'border-green-100 bg-green-50/50';
                                            $badgeClass = 'badge-success';
                                            $statusText = 'Disetujui';
                                            $icon = '✓';
                                        } elseif ($isRejected) {
                                            $dotClass = 'bg-destructive ring-red-100';
                                            $cardClass = 'border-red-100 bg-red-50/50';
                                            $badgeClass = 'badge-destructive';
                                            $statusText = 'Ditolak';
                                            $icon = '✕';
                                        } elseif ($isRevision) {
                                            $dotClass = 'bg-amber-500 ring-amber-100';
                                            $cardClass = 'border-amber-100 bg-amber-50/50';
                                            $badgeClass = 'badge-warning';
                                            $statusText = 'Revisi';
                                            $icon = '↻';
                                        } elseif ($isPending) {
                                            $dotClass = 'bg-amber-400 ring-amber-100 animate-pulse';
                                            $cardClass = 'border-amber-100 bg-amber-50/30';
                                            $badgeClass = 'badge-warning';
                                            $statusText = 'Menunggu';
                                            $icon = '●';
                                        } else {
                                            $dotClass = 'bg-muted-foreground/30 ring-muted';
                                            $cardClass = 'border-border bg-muted/20';
                                            $badgeClass = 'badge-secondary';
                                            $statusText = 'Belum Sampai';
                                            $icon = '—';
                                        }

                                        if ($step->isDivisionLevel()) {
                                            $stepLabel = 'Level ≤ ' . $step->required_level . ' (Divisi)';
                                        } elseif ($step->isRoleLevel()) {
                                            $stepLabel = ($step->role?->name ?? '—') . ' (Level ≤ ' . $step->required_level . ')';
                                        } else {
                                            $stepLabel = $step->role?->name ?? '—';
                                        }
                                    @endphp
                                    <div class="relative flex items-start pl-12 pb-5 last:pb-0">
                                        <div
                                            class="absolute left-3 top-1.5 w-5 h-5 rounded-full ring-4 ring-background flex items-center justify-center text-white text-xs font-bold z-10 {{ $dotClass }}">
                                            {!! $icon !!}
                                        </div>
                                        <div class="flex-1 border rounded-xl p-4 {{ $cardClass }}">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-muted-foreground">Step
                                                        {{ $step->step_order }}</span>
                                                    <span class="text-sm font-semibold text-foreground">{{ $stepLabel }}</span>
                                                </div>
                                                <span class="{{ $badgeClass }}">{{ $statusText }}</span>
                                            </div>
                                            @if($approval && $isDone)
                                                <div class="mt-2 pt-2 border-t border-black/5 text-sm space-y-1">
                                                    <p class="text-xs text-muted-foreground">Oleh: <span
                                                            class="font-medium text-foreground">{{ $approval->approver?->name ?? '—' }}</span>
                                                    </p>
                                                    <p class="text-xs text-muted-foreground">Tanggal:
                                                        {{ $approval->updated_at?->format('d M Y, H:i') ?? '—' }}</p>
                                                    @if($approval->comments)
                                                        <div
                                                            class="mt-1 p-2 bg-background/60 rounded-md text-xs italic text-muted-foreground border border-border">
                                                            "{{ $approval->comments }}"
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($isPending)
                                                <p class="mt-2 text-xs text-amber-600 italic">⏳ Menunggu approver meninjau…</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Item Pengajuan</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="w-10">#</th>
                                <th>Item</th>
                                <th>Deskripsi</th>
                                <th class="text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->items as $i => $item)
                                <tr>
                                    <td class="text-muted-foreground">{{ $i + 1 }}</td>
                                    <td><span
                                            class="badge-secondary capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                                    </td>
                                    <td class="text-muted-foreground">{{ $item->description ?: '—' }}</td>
                                    <td class="text-right font-bold text-foreground">Rp
                                        {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-muted/30">
                                <td colspan="3" class="text-right font-bold text-muted-foreground">Total</td>
                                <td class="text-right font-bold text-primary text-base">Rp
                                    {{ number_format($report->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Attachments --}}
            @if($report->attachments->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 border-b border-border">
                        <h3 class="card-title">Lampiran</h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($report->attachments as $att)
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                                class="flex items-center gap-3 p-3 border border-border rounded-xl hover:bg-accent hover:border-primary/20 transition-colors group">
                                <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-foreground truncate group-hover:text-primary">
                                        {{ basename($att->file_path) }}</p>
                                    <p class="text-xs text-muted-foreground capitalize">{{ $att->file_type }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>