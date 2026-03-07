<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('travel-reports.index') }}"
                    class="text-muted-foreground hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-foreground">Detail LHP</h2>
                    <p class="text-sm text-muted-foreground font-mono">
                        LHP-{{ str_pad($travelReport->id, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>
            <a href="{{ route('travel-reports.print', $travelReport) }}" target="_blank" class="btn-default btn-sm">
                🖨️ Cetak LHP
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Header Card --}}
            <div class="card p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Status</p>
                            <div class="mt-1">
                                @php
                                    $badgeClass = match ($travelReport->status) {
                                        'submitted' => 'badge-info',
                                        'approved' => 'badge-success',
                                        'draft' => 'badge-secondary',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span class="{{ $badgeClass }}">{{ strtoupper($travelReport->status) }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Pembuat LHP
                            </p>
                            <p class="text-sm font-medium text-foreground mt-1">{{ $travelReport->user->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $travelReport->user->division?->name ?? '—' }} ·
                                {{ $travelReport->job_position }}
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Kota Tujuan
                            </p>
                            <p class="text-sm font-semibold text-foreground mt-1">{{ $travelReport->destination_city }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tanggal</p>
                            <p class="text-sm text-foreground mt-1">
                                {{ $travelReport->departure_date->format('d M Y') }} —
                                {{ $travelReport->return_date->format('d M Y') }}
                            </p>
                            <p class="text-xs text-muted-foreground">
                                {{ $travelReport->departure_date->diffInDays($travelReport->return_date) + 1 }} hari
                            </p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @if($travelReport->surat_tugas_no)
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Surat Tugas
                                </p>
                                <p class="text-sm font-medium text-foreground mt-1">{{ $travelReport->surat_tugas_no }}</p>
                                @if($travelReport->surat_tugas_date)
                                    <p class="text-xs text-muted-foreground">
                                        {{ $travelReport->surat_tugas_date->format('d M Y') }}
                                    </p>
                                @endif
                            </div>
                        @endif
                        @if($travelReport->request)
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Request
                                    Terkait</p>
                                <a href="{{ route('requests.show', $travelReport->request) }}"
                                    class="text-sm text-primary hover:underline mt-1 block">
                                    {{ $travelReport->request->title }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Maksud & Tujuan --}}
            <div class="card p-6">
                <h3 class="card-title mb-3">🎯 Maksud dan Tujuan</h3>
                <p class="text-sm text-foreground leading-relaxed">{{ $travelReport->purpose }}</p>
            </div>

            {{-- Kegiatan (Grouped) --}}
            @foreach($travelReport->activities as $activity)
                <div class="card overflow-hidden">
                    <div class="px-6 py-4 bg-primary/5 border-b border-border flex items-center gap-3">
                        <span
                            class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold shrink-0">
                            {{ $loop->iteration }}
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-foreground">{{ $activity->activity_date->format('d F Y') }}
                            </h3>
                            <p class="text-xs text-muted-foreground">Kegiatan {{ $loop->iteration }}</p>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        {{-- Pelaksanaan --}}
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">📋
                                Pelaksanaan</p>
                            <p class="text-sm text-foreground">{{ $activity->description }}</p>
                        </div>

                        {{-- Hasil --}}
                        @if($activity->results && count($activity->results))
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">✅ Hasil</p>
                                <ol class="list-decimal list-inside space-y-0.5">
                                    @foreach($activity->results as $result)
                                        <li class="text-sm text-foreground">{{ $result }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        {{-- Permasalahan --}}
                        @if($activity->issues)
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">⚠️
                                    Permasalahan</p>
                                <p class="text-sm text-foreground">{{ $activity->issues }}</p>
                            </div>
                        @endif

                        {{-- Kesimpulan --}}
                        @if($activity->conclusion)
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">📝
                                    Kesimpulan</p>
                                <p class="text-sm text-foreground">{{ $activity->conclusion }}</p>
                            </div>
                        @endif

                        {{-- Dokumentasi --}}
                        @if($activity->documents->isNotEmpty())
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">📸
                                    Dokumentasi</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($activity->documents as $doc)
                                        <div class="border border-border rounded-lg overflow-hidden group">
                                            @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $doc->caption }}"
                                                    class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-200">
                                            @else
                                                <div class="w-full h-32 bg-muted/30 flex items-center justify-center">
                                                    <span class="text-xs text-muted-foreground">📄
                                                        {{ basename($doc->file_path) }}</span>
                                                </div>
                                            @endif
                                            @if($doc->caption)
                                                <div class="p-2">
                                                    <p class="text-xs text-foreground">{{ $doc->caption }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Kesimpulan & Rekomendasi Umum --}}
            <div class="card p-6">
                <h3 class="card-title mb-3">📋 Kesimpulan & Rekomendasi Umum</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Kesimpulan
                        </p>
                        <p class="text-sm text-foreground">{{ $travelReport->conclusion }}</p>
                    </div>
                    @if($travelReport->recommendations && count($travelReport->recommendations))
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Rekomendasi
                                Tindak Lanjut</p>
                            <ol class="list-decimal list-inside space-y-1">
                                @foreach($travelReport->recommendations as $rec)
                                    <li class="text-sm text-foreground">{{ $rec }}</li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Approval Chain --}}
            <div class="card p-6">
                <h3 class="card-title mb-4">🔗 Status Approval</h3>
                @php
                    $steps = \App\Models\TravelReportApproval::STEPS;
                    $existingApprovals = $travelReport->approvals->keyBy('step');
                    $approvalStatusBadge = match ($travelReport->approval_status) {
                        'approved' => 'badge-success',
                        'rejected' => 'badge-destructive',
                        'in_review' => 'badge-warning',
                        default => 'badge-secondary',
                    };
                    $approvalStatusLabel = ['approved' => 'Disetujui', 'rejected' => 'Ditolak', 'in_review' => 'Dalam Review', 'draft' => 'Draft'][$travelReport->approval_status] ?? $travelReport->approval_status;
                @endphp
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-sm font-medium text-foreground">Status Keseluruhan:</span>
                    <span class="badge {{ $approvalStatusBadge }}">{{ $approvalStatusLabel }}</span>
                </div>
                <div class="space-y-1">
                    @foreach($steps as $key => $def)
                        @php $a = $existingApprovals->get($key); @endphp
                        <div class="flex items-center gap-3 py-2.5 border-b border-border last:border-0">
                            <div @class([
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0',
                                'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' => $a && $a->status === 'approved',
                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' => $a && $a->status === 'rejected',
                                'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-600' => $a && $a->status === 'pending',
                                'bg-muted text-muted-foreground' => !$a,
                            ])>
                                @if($a && $a->status === 'approved') ✓
                                @elseif($a && $a->status === 'rejected') ✗
                                @elseif($a && $a->status === 'pending') ·
                                @else —
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-foreground">{{ $def['label'] }}</span>
                                @if($a && $a->approver)
                                    <span class="text-xs text-muted-foreground ml-2">
                                        {{ $a->approver->name }} · {{ $a->updated_at->format('d M Y, H:i') }}
                                    </span>
                                @elseif($a && $a->status === 'pending')
                                    <span class="text-xs text-yellow-600 ml-2">Menunggu approval…</span>
                                @elseif(!$a)
                                    <span class="text-xs text-muted-foreground ml-2">Dilewati / Belum giliran</span>
                                @endif
                                @if($a && $a->comments)
                                    <p class="text-xs text-muted-foreground mt-0.5 italic">"{{ $a->comments }}"</p>
                                @endif
                            </div>
                            @if($a)
                                <span @class([
                                    'badge text-xs shrink-0',
                                    'badge-success' => $a->status === 'approved',
                                    'badge-destructive' => $a->status === 'rejected',
                                    'badge-warning' => $a->status === 'pending',
                                ])>
                                    {{ ['approved' => 'Disetujui', 'rejected' => 'Ditolak', 'pending' => 'Pending'][$a->status] ?? $a->status }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>