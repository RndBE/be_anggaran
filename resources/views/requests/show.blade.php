<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('requests.index') }}"
                class="text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-foreground">{{ $request->title }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Detail Pengajuan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Left: Details & Items -->
                <div class="md:col-span-2 space-y-5">

                    <!-- Header Info -->
                    <div class="card p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tipe</p>
                                <p class="text-lg font-semibold text-foreground mt-1">{{ ucfirst($request->type) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">
                                    Status</p>
                                @php
                                    $badgeClass = match ($request->status) {
                                        'approved', 'paid' => 'badge-success',
                                        'rejected' => 'badge-destructive',
                                        'revision_requested' => 'badge-purple',
                                        'pending' => 'badge-warning',
                                        default => 'badge-secondary',
                                    };
                                @endphp
                                <span
                                    class="{{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                            </div>
                        </div>
                        <div class="separator mb-4"></div>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Kode Klien</p>
                                <p
                                    class="mt-1 font-medium text-foreground bg-muted/50 px-3 py-1.5 rounded-md inline-block text-sm border border-border">
                                    {{ $request->clientCode ? $request->clientCode->prefix . '-' . $request->clientCode->instansi_singkat : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total
                                    Jumlah</p>
                                <p class="text-2xl font-bold text-primary mt-1">Rp
                                    {{ number_format($request->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if($request->description)
                            <div class="mt-4">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Deskripsi
                                </p>
                                <p class="mt-1 text-sm text-foreground leading-relaxed">{{ $request->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Line Items -->
                    <div class="card overflow-hidden">
                        <div class="px-6 py-4 border-b border-border">
                            <h3 class="card-title">Daftar Item</h3>
                        </div>
                        <div class="divide-y divide-border">
                            @foreach($request->items as $item)
                                <div class="p-5">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="badge-info capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                                            @if($item->type === 'entertain')
                                                <span class="badge-destructive">Butuh Direktur</span>
                                            @endif
                                        </div>
                                        <span class="font-bold text-foreground">Rp
                                            {{ number_format($item->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{{ $item->description }}</p>
                                    @if($item->attachments->count() > 0)
                                        <div class="mt-3 pt-3 border-t border-border">
                                            <p class="text-xs font-semibold text-muted-foreground uppercase mb-2">Lampiran
                                            </p>
                                            <div class="flex gap-2 flex-wrap">
                                                @foreach($item->attachments as $att)
                                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                        class="btn-outline btn-sm">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        Lihat File
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right: Approval Timeline -->
                <div>
                    <div class="card p-6 sticky top-20">
                        <h3 class="card-title mb-5">Riwayat Persetujuan</h3>
                        <div class="space-y-5">
                            <!-- Submit node -->
                            <div class="relative pl-6 border-l-2 border-primary/30">
                                <div
                                    class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-primary ring-4 ring-background">
                                </div>
                                <p class="text-sm font-semibold text-foreground">Diajukan</p>
                                <p class="text-xs text-muted-foreground">
                                    {{ $request->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-primary mt-0.5">Oleh: {{ $request->user->name }}</p>
                            </div>

                            @foreach($request->approvals as $approval)
                                @php
                                    $nodeColor = match ($approval->status) {
                                        'approved' => 'bg-green-500',
                                        'rejected' => 'bg-destructive',
                                        'revision' => 'bg-amber-500',
                                        default => 'bg-muted-foreground/40',
                                    };
                                    $lineColor = match ($approval->status) {
                                        'approved' => 'border-green-200',
                                        'rejected' => 'border-red-200',
                                        'revision' => 'border-amber-200',
                                        default => 'border-muted',
                                    };
                                    $textColor = match ($approval->status) {
                                        'approved' => 'text-green-600',
                                        'rejected' => 'text-destructive',
                                        'revision' => 'text-amber-600',
                                        default => 'text-muted-foreground',
                                    };
                                @endphp
                                <div class="relative pl-6 border-l-2 {{ $lineColor }}">
                                    <div
                                        class="absolute -left-[9px] top-1 h-4 w-4 rounded-full {{ $nodeColor }} ring-4 ring-background">
                                    </div>
                                    <p class="text-sm font-semibold text-foreground">
                                        {{ $approval->step->role->name ?? 'Approver' }}</p>
                                    <span
                                        class="text-xs font-bold uppercase {{ $textColor }}">{{ $approval->status }}</span>
                                    @if($approval->approver)
                                        <p class="text-xs text-muted-foreground mt-0.5">
                                            {{ $approval->updated_at->format('d M Y, H:i') }}</p>
                                        <p class="text-xs text-muted-foreground">Oleh: {{ $approval->approver->name }}</p>
                                    @endif
                                    @if($approval->comments)
                                        <div
                                            class="mt-2 bg-muted/30 rounded-md p-2 text-xs italic text-muted-foreground border border-border">
                                            "{{ $approval->comments }}"
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>