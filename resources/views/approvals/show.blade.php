<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('approvals.index') }}"
                class="text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-foreground">{{ __('Tinjau Pengajuan') }}</h2>
                <p class="text-sm text-muted-foreground mt-0.5">{{ $approval->request->title }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- Left: Request Details -->
                <div class="card p-6">
                    <h3 class="card-title mb-4">Detail Pengajuan</h3>
                    <div class="separator mb-4"></div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Pemohon
                            </p>
                            <p class="text-sm font-medium text-foreground mt-1">{{ $approval->request->user->name }}</p>
                            <p class="text-xs text-muted-foreground">{{ $approval->request->user->email }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Tipe</p>
                                <span class="badge-secondary mt-1 capitalize">{{ $approval->request->type }}</span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Kode
                                    Klien</p>
                                <p class="text-sm text-foreground mt-1">
                                    {{ $approval->request->clientCode ? $approval->request->clientCode->prefix . '-' . $approval->request->clientCode->instansi_singkat : 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Total Jumlah
                            </p>
                            <p class="text-2xl font-bold text-primary mt-1">Rp
                                {{ number_format($approval->request->total_amount, 0, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-2">Rincian
                                Item</p>
                            <div class="space-y-2">
                                @foreach($approval->request->items as $item)
                                    <div class="bg-muted/30 rounded-lg border border-border p-3">
                                        <div class="flex justify-between items-start">
                                            <span
                                                class="badge-secondary capitalize">{{ str_replace('_', ' ', $item->type) }}</span>
                                            <span class="font-bold text-foreground text-sm">Rp
                                                {{ number_format($item->amount, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="text-xs text-muted-foreground mt-1.5">{{ $item->description }}</p>
                                        @if($item->attachments->count() > 0)
                                            <div class="mt-2 flex gap-2 flex-wrap">
                                                @foreach($item->attachments as $att)
                                                    <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                        class="btn-outline btn-sm">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        Lampiran
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Approval Action -->
                <div>
                    @auth
                        @php
                            $step = $approval->step;
                            $authUser = Auth::user();
                            if ($step->isDivisionLevel()) {
                                $requesterDivId = $approval->request->user->division_id;
                                $canAct = $authUser->division_id === $requesterDivId
                                    && $authUser->level !== null
                                    && $authUser->level <= $step->required_level;
                            } elseif ($step->isRoleLevel()) {
                                $canAct = $step->role !== null
                                    && $authUser->hasRole($step->role->slug)
                                    && $authUser->level !== null
                                    && $authUser->level <= $step->required_level;
                            } else {
                                $canAct = $step->role !== null && $authUser->hasRole($step->role->slug);
                            }
                            $stepLabel = $step->isDivisionLevel()
                                ? 'Level ≤ ' . $step->required_level . ' (Divisi)'
                                : ($step->role?->name ?? '—');
                        @endphp

                        @if($canAct)
                            <div class="card p-6">
                                <h3 class="card-title mb-1">Keputusan Anda</h3>
                                <p class="card-description">Tinjau pengajuan dan buat keputusan persetujuan</p>
                                <div class="separator my-4"></div>

                                <form action="{{ route('approvals.update', $approval->id) }}" method="POST" class="space-y-5">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <x-input-label>Tahap Persetujuan</x-input-label>
                                        <div
                                            class="mt-1 px-3 py-2 bg-muted/30 rounded-md border border-border text-sm font-mono text-foreground">
                                            {{ $stepLabel }} (Step {{ $step->step_order }})
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <x-input-label>Keputusan</x-input-label>
                                        <div class="mt-1 space-y-2">
                                            <label
                                                class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-green-50 hover:border-green-200 transition-colors">
                                                <input type="radio" name="status" value="approved"
                                                    class="text-green-600 focus:ring-green-500" required>
                                                <div>
                                                    <p class="text-sm font-semibold text-green-700">✓ Setujui</p>
                                                    <p class="text-xs text-muted-foreground">Setujui pengajuan ini</p>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-amber-50 hover:border-amber-200 transition-colors">
                                                <input type="radio" name="status" value="revision"
                                                    class="text-amber-600 focus:ring-amber-500">
                                                <div>
                                                    <p class="text-sm font-semibold text-amber-700">↩ Minta Revisi</p>
                                                    <p class="text-xs text-muted-foreground">Minta revisi dari pemohon</p>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center gap-3 p-3 border border-border rounded-lg cursor-pointer hover:bg-red-50 hover:border-red-200 transition-colors">
                                                <input type="radio" name="status" value="rejected"
                                                    class="text-destructive focus:ring-destructive">
                                                <div>
                                                    <p class="text-sm font-semibold text-destructive">✕ Tolak</p>
                                                    <p class="text-xs text-muted-foreground">Tolak pengajuan ini sepenuhnya</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <x-input-label for="comments">Komentar / Alasan</x-input-label>
                                        <textarea name="comments" id="comments" rows="3" class="textarea"
                                            placeholder="Wajib diisi jika menolak atau meminta revisi…"></textarea>
                                    </div>

                                    <div class="flex justify-end gap-3 pt-2 border-t border-border">
                                        <a href="{{ route('approvals.index') }}" class="btn-outline">Batal</a>
                                        <button type="submit" class="btn-default">Kirim Keputusan</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="card p-8 flex flex-col items-center justify-center text-center">
                                <div class="w-14 h-14 rounded-full bg-muted flex items-center justify-center mb-4">
                                    <svg class="w-7 h-7 text-muted-foreground" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-foreground">Hanya Melihat</p>
                                <p class="text-sm text-muted-foreground mt-2">
                                    Tahap persetujuan ini membutuhkan peran
                                    <span class="badge-warning mx-1">{{ $approval->step->role->name ?? '—' }}</span>.
                                </p>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>