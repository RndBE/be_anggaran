<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('travel-report-approvals.index') }}"
                class="text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-foreground">Review LHP — {{ $travelReportApproval->step_label }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Perjalanan ke
                    {{ $travelReportApproval->travelReport->destination_city }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @php $report = $travelReportApproval->travelReport; @endphp

            {{-- Identity Card --}}
            <div class="card p-6">
                <h3 class="card-title mb-4">📋 Identitas Perjalanan Dinas</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Nama</p>
                        <p class="font-medium text-foreground">{{ $report->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Divisi</p>
                        <p class="font-medium text-foreground">{{ $report->user->division->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Kota Tujuan</p>
                        <p class="font-medium text-foreground">{{ $report->destination_city }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Tanggal</p>
                        <p class="font-medium text-foreground">
                            {{ $report->departure_date->format('d M Y') }} – {{ $report->return_date->format('d M Y') }}
                        </p>
                    </div>
                    @if($report->surat_tugas_no)
                        <div>
                            <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Surat Tugas</p>
                            <p class="font-medium text-foreground">{{ $report->surat_tugas_no }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-muted-foreground text-xs uppercase tracking-wide mb-1">Tujuan</p>
                        <p class="font-medium text-foreground">{{ $report->purpose }}</p>
                    </div>
                </div>
            </div>

            {{-- Activities Summary --}}
            <div class="card p-6">
                <h3 class="card-title mb-4">📅 Pelaksanaan Kegiatan</h3>
                <div class="space-y-4">
                    @foreach($report->activities as $activity)
                        <div class="bg-muted/30 rounded-lg p-4 border border-border">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="text-xs font-semibold text-primary">{{ $activity->activity_date->format('d M Y') }}</span>
                            </div>
                            <p class="text-sm text-foreground">{{ $activity->description }}</p>
                            @if($activity->results && count($activity->results))
                                <ul class="mt-2 list-disc pl-4 space-y-0.5 text-sm text-muted-foreground">
                                    @foreach($activity->results as $r)
                                        <li>{{ $r }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Conclusion --}}
            <div class="card p-6">
                <h3 class="card-title mb-3">📝 Kesimpulan</h3>
                <p class="text-sm text-foreground">{{ $report->conclusion }}</p>
            </div>

            {{-- Approval Chain Status --}}
            <div class="card p-6">
                <h3 class="card-title mb-4">🔗 Status Approval Chain</h3>
                <div class="space-y-2">
                    @php
                        $steps = \App\Models\TravelReportApproval::STEPS;
                        $existingApprovals = $report->approvals->keyBy('step');
                    @endphp
                    @foreach($steps as $key => $def)
                        @php $a = $existingApprovals->get($key); @endphp
                        <div class="flex items-center gap-3 py-2">
                            <div @class([
                                'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold',
                                'bg-success/20 text-success' => $a && $a->status === 'approved',
                                'bg-destructive/20 text-destructive' => $a && $a->status === 'rejected',
                                'bg-warning/20 text-warning' => $a && $a->status === 'pending',
                                'bg-muted text-muted-foreground' => !$a,
                            ])>
                                @if($a && $a->status === 'approved') ✓
                                @elseif($a && $a->status === 'rejected') ✗
                                @elseif($a && $a->status === 'pending') ⏳
                                @else —
                                @endif
                            </div>
                            <div class="flex-1">
                                <span class="text-sm font-medium text-foreground">{{ $def['label'] }}</span>
                                @if($a && $a->approver)
                                    <span class="text-xs text-muted-foreground ml-2">{{ $a->approver->name }} ·
                                        {{ $a->updated_at->format('d M Y') }}</span>
                                @elseif(!$a)
                                    <span class="text-xs text-muted-foreground ml-2">Menunggu / Dilewati</span>
                                @endif
                            </div>
                            @if($a)
                                <span @class([
                                    'badge text-xs',
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

            {{-- Action Form --}}
            @if($travelReportApproval->status === 'pending')
                <div class="card p-6">
                    <h3 class="card-title mb-4">⚖️ Keputusan Anda — {{ $travelReportApproval->step_label }}</h3>
                    <form method="POST" action="{{ route('travel-report-approvals.update', $travelReportApproval) }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-group mb-4">
                            <label class="label">Komentar / Catatan <span
                                    class="text-muted-foreground font-normal text-xs">(opsional)</span></label>
                            <textarea name="comments" class="textarea" rows="3"
                                placeholder="Tuliskan catatan jika ada..."></textarea>
                        </div>

                        @if($errors->any())
                            <div class="alert-destructive mb-4 text-sm">{{ $errors->first() }}</div>
                        @endif

                        <div class="flex gap-3">
                            <button type="submit" name="status" value="approved"
                                class="btn-default flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Setujui
                            </button>
                            <button type="submit" name="status" value="rejected"
                                class="btn-destructive flex items-center gap-2"
                                onclick="return confirm('Yakin menolak LHP ini?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Tolak
                            </button>
                            <a href="{{ route('travel-reports.print', $report) }}" target="_blank"
                                class="btn-outline flex items-center gap-2 ml-auto">
                                🖨️ Preview PDF
                            </a>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>