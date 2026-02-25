<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                Detail Report:
                <span class="font-mono text-indigo-600">REQ-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span>
            </h2>
            <a href="{{ route('reports.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-3">

            {{-- ── Header Info ──────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Left --}}
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Status</span>
                            @php
                                $statusColors = [
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'revision' => 'bg-orange-100 text-orange-800',
                                ];
                            @endphp
                            <div class="mt-1">
                                <span
                                    class="px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColors[$report->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ strtoupper($report->status) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Pemohon</span>
                            <p class="text-sm font-medium text-gray-900">{{ $report->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $report->user->division?->name ?? '—' }} · Level
                                {{ $report->user->level ?? '—' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Tanggal Pengajuan</span>
                            <p class="text-sm text-gray-700">{{ $report->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    {{-- Center --}}
                    <div class="space-y-3">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Judul</span>
                            <p class="text-sm font-semibold text-gray-900">{{ $report->title }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Tipe</span>
                            <p class="text-sm text-gray-700 capitalize">{{ $report->type }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase">Client Code</span>
                            <p class="text-sm text-gray-700">
                                {{ $report->clientCode ? $report->clientCode->prefix . '-' . $report->clientCode->instansi_singkat : '—' }}
                            </p>
                        </div>
                    </div>
                    {{-- Right --}}
                    <div class="flex flex-col items-end justify-between">
                        <div class="text-right">
                            <span class="text-xs font-semibold text-gray-400 uppercase">Total Anggaran</span>
                            <p class="text-2xl font-bold text-indigo-600 mt-1">Rp
                                {{ number_format($report->total_amount, 0, ',', '.') }}</p>
                        </div>
                        @if($report->description)
                            <div class="text-right mt-3 w-full">
                                <span class="text-xs font-semibold text-gray-400 uppercase">Deskripsi</span>
                                <p class="text-sm text-gray-600 mt-0.5">{{ $report->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Peserta Dinas ────────────────────────────────────── --}}
            @if($report->participants->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
                    <h3 class="font-semibold text-gray-800 mb-3">👥 Peserta Dinas</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($report->participants as $p)
                            <span class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-800 text-sm rounded-full font-medium">
                                <span class="w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($p->name, 0, 1)) }}
                                </span>
                                {{ $p->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Approval Progress ────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Progress Approval</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Riwayat perjalanan approval sampai tahap saat ini</p>
                </div>
                <div class="p-6">
                    @if($allSteps->isEmpty())
                        <p class="text-sm text-gray-500 italic text-center py-4">Belum ada flow approval.</p>
                    @else
                        <div class="relative">
                            {{-- Vertical line --}}
                            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                            <div class="space-y-0">
                                @foreach($allSteps as $step)
                                    @php
                                        $approval = $approvalsByStep->get($step->id);
                                        $isApproved = $approval && $approval->status === 'approved';
                                        $isPending = $approval && $approval->status === 'pending';
                                        $isRejected = $approval && $approval->status === 'rejected';
                                        $isRevision = $approval && $approval->status === 'revision';
                                        $isSkipped = !$approval && !$isPending;
                                        $isDone = $isApproved || $isRejected || $isRevision;

                                        // Determine step state for visuals
                                        if ($isApproved) {
                                            $dotClass = 'bg-green-500 ring-green-200';
                                            $lineClass = 'border-green-200 bg-green-50';
                                            $badge = 'bg-green-100 text-green-700';
                                            $statusText = 'Disetujui';
                                            $icon = '✓';
                                        } elseif ($isRejected) {
                                            $dotClass = 'bg-red-500 ring-red-200';
                                            $lineClass = 'border-red-200 bg-red-50';
                                            $badge = 'bg-red-100 text-red-700';
                                            $statusText = 'Ditolak';
                                            $icon = '✕';
                                        } elseif ($isRevision) {
                                            $dotClass = 'bg-orange-500 ring-orange-200';
                                            $lineClass = 'border-orange-200 bg-orange-50';
                                            $badge = 'bg-orange-100 text-orange-700';
                                            $statusText = 'Revisi';
                                            $icon = '↻';
                                        } elseif ($isPending) {
                                            $dotClass = 'bg-yellow-400 ring-yellow-200 animate-pulse';
                                            $lineClass = 'border-yellow-200 bg-yellow-50';
                                            $badge = 'bg-yellow-100 text-yellow-700';
                                            $statusText = 'Menunggu Approval';
                                            $icon = '●';
                                        } else {
                                            // Skipped / belum sampai
                                            $dotClass = 'bg-gray-300 ring-gray-100';
                                            $lineClass = 'border-gray-100 bg-gray-50';
                                            $badge = 'bg-gray-100 text-gray-500';
                                            $statusText = 'Belum Sampai';
                                            $icon = '—';
                                        }

                                        // Step label
                                        if ($step->isDivisionLevel()) {
                                            $stepLabel = 'Level ≤ ' . $step->required_level . ' (Divisi)';
                                        } elseif ($step->isRoleLevel()) {
                                            $stepLabel = ($step->role?->name ?? '—') . ' (Level ≤ ' . $step->required_level . ')';
                                        } else {
                                            $stepLabel = $step->role?->name ?? '—';
                                        }
                                    @endphp

                                    <div class="relative flex items-start pl-12 pb-6 last:pb-0">
                                        {{-- Dot --}}
                                        <div
                                            class="absolute left-3 top-1.5 w-5 h-5 rounded-full ring-4 flex items-center justify-center text-white text-xs font-bold z-10 {{ $dotClass }}">
                                            {!! $icon !!}
                                        </div>

                                        {{-- Content card --}}
                                        <div class="flex-1 border rounded-xl p-4 {{ $lineClass }}">
                                            <div class="flex items-center justify-between flex-wrap gap-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-400">Step
                                                        {{ $step->step_order }}</span>
                                                    <span class="text-sm font-semibold text-gray-800">{{ $stepLabel }}</span>
                                                </div>
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badge }}">{{ $statusText }}</span>
                                            </div>

                                            @if($approval && $isDone)
                                                <div class="mt-2 pt-2 border-t border-gray-200/50 text-sm space-y-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500">Oleh:</span>
                                                        <span
                                                            class="font-medium text-gray-800">{{ $approval->approver?->name ?? '—' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500">Tanggal:</span>
                                                        <span
                                                            class="text-gray-700">{{ $approval->updated_at?->format('d M Y, H:i') ?? '—' }}</span>
                                                    </div>
                                                    @if($approval->comments)
                                                        <div class="mt-1 p-2 bg-white/60 rounded-lg text-sm text-gray-600 italic">
                                                            "{{ $approval->comments }}"
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($isPending)
                                                <p class="mt-2 text-xs text-yellow-600 italic">⏳ Sedang menunggu approver untuk
                                                    meninjau…</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Items ─────────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Item Pengajuan</h3>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($report->items as $i => $item)
                                <tr>
                                    <td class="px-6 py-3 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800 capitalize">
                                        {{ str_replace('_', ' ', $item->type) }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $item->description ?: '—' }}</td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-900">Rp
                                        {{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-700">Total</td>
                                <td class="px-6 py-3 text-right font-bold text-indigo-600 text-base">Rp
                                    {{ number_format($report->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Attachments ───────────────────────────────────────── --}}
            @if($report->attachments->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800">Lampiran</h3>
                    </div>
                    <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($report->attachments as $att)
                            <a href="{{ asset('storage/' . $att->file_path) }}" target="_blank"
                                class="flex items-center gap-3 p-3 border rounded-xl hover:bg-gray-50 transition-colors group">
                                @if(str_starts_with(mime_content_type(storage_path('app/public/' . $att->file_path)) ?? '', 'image'))
                                    <img src="{{ asset('storage/' . $att->file_path) }}" class="w-12 h-12 object-cover rounded"
                                        alt="attachment">
                                @else
                                    <div class="w-12 h-12 bg-red-50 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-gray-800 truncate group-hover:text-indigo-600">
                                        {{ basename($att->file_path) }}</p>
                                    <p class="text-xs text-gray-400 capitalize">{{ $att->file_type }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>