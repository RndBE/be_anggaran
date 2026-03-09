<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">Alur Persetujuan</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Rancang urutan approval untuk pengajuan</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Create New Flow --}}
            <div class="card p-6" x-data="flowBuilder()">
                <h3 class="card-title">Buat Alur Baru</h3>
                <p class="card-description mt-1 mb-5">Rancang urutan persetujuan yang dikustomisasi.</p>
                <div class="separator mb-5"></div>

                <form method="POST" action="{{ route('settings.flows.store') }}" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <x-input-label for="name">Nama Alur</x-input-label>
                            <x-text-input id="name" name="name" type="text" required autofocus
                                placeholder="cth. Alur Approval Marketing" />
                        </div>
                        <div class="form-group">
                            <x-input-label for="description">Deskripsi</x-input-label>
                            <x-text-input id="description" name="description" type="text"
                                placeholder="Penjelasan opsional singkat" />
                        </div>
                    </div>

                    <div class="form-group">
                        <x-input-label>Tipe Alur</x-input-label>
                        <div class="mt-1 flex gap-4">
                            <label
                                class="flex items-center gap-2 p-3 border border-border rounded-lg cursor-pointer hover:bg-accent transition-colors">
                                <input type="radio" name="flow_type" value="request" checked
                                    class="text-primary focus:ring-ring">
                                <div>
                                    <p class="text-sm font-semibold text-foreground">Pengajuan (Request)</p>
                                    <p class="text-xs text-muted-foreground">Untuk anggaran & reimbursement</p>
                                </div>
                            </label>
                            <label
                                class="flex items-center gap-2 p-3 border border-border rounded-lg cursor-pointer hover:bg-accent transition-colors">
                                <input type="radio" name="flow_type" value="lhp" class="text-primary focus:ring-ring">
                                <div>
                                    <p class="text-sm font-semibold text-foreground">LHP (Laporan Perjalanan)</p>
                                    <p class="text-xs text-muted-foreground">Untuk laporan hasil perjalanan dinas</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-foreground mb-1">Urutan Step Persetujuan</h4>
                        <p class="text-xs text-muted-foreground mb-3">Tentukan siapa yang harus menyetujui, dari yang
                            pertama
                            sampai terakhir.</p>

                        <div class="space-y-2">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex items-center gap-3 bg-muted/30 border border-border p-3 rounded-lg">
                                    <div
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary font-bold text-sm shrink-0">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    <div class="flex-1">
                                        <select x-model="step.role_id" :name="`steps[${index}][role_id]`"
                                            class="select-input" required>
                                            <option value="" disabled>Pilih Peran…</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                        <input type="checkbox" :name="`steps[${index}][requires_director]`" value="1"
                                            x-model="step.requires_director"
                                            class="rounded border-input text-primary focus:ring-ring">
                                        <span class="text-xs text-muted-foreground">Pengecualian Dir.?</span>
                                    </label>
                                    <button type="button" @click="removeStep(index)" x-show="steps.length > 1"
                                        class="text-destructive hover:text-destructive/80 transition-colors shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addStep" class="btn-outline btn-sm mt-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Step
                        </button>
                    </div>

                    <div class="pt-4 border-t border-border flex justify-end">
                        <x-primary-button>Simpan Alur</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Existing Flows --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Alur Persetujuan yang Ada</h3>
                </div>
                <div class="p-5 space-y-3">
                    @if(session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->has('error'))
                        <div class="alert-destructive">{{ $errors->first('error') }}</div>
                    @endif

                    @forelse($flows as $flow)
                        <div class="border border-border rounded-xl p-5 hover:border-primary/30 transition-colors">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-bold text-foreground">{{ $flow->name }}</h3>
                                    <p class="text-sm text-muted-foreground mt-0.5">
                                        {{ $flow->description ?? 'Tanpa deskripsi' }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-4">
                                    <span class="badge-success">Aktif</span>
                                    @if($flow->flow_type === 'lhp')
                                        <span class="badge-warning">LHP</span>
                                    @else
                                        <span class="badge-info">Request</span>
                                    @endif
                                    <a href="{{ route('settings.flows.edit', $flow) }}" class="btn-outline btn-sm">Ubah</a>
                                    <form method="POST" action="{{ route('settings.flows.destroy', $flow) }}"
                                        onsubmit="return confirm('Hapus flow «{{ $flow->name }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-destructive btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div class="flex items-center flex-wrap gap-1.5 text-sm">
                                <span
                                    class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Start</span>
                                @foreach($flow->steps as $step)
                                    <svg class="w-4 h-4 text-muted-foreground/40 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                    <div
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold
                                                        {{ $step->requires_director ? 'bg-red-50 border border-dashed border-red-200 text-red-700' : 'bg-primary/5 border border-primary/20 text-primary' }}">
                                        <span class="text-muted-foreground font-normal">{{ $step->step_order }}.</span>
                                        @if($step->required_level)
                                            Lv≤{{ $step->required_level }} <span class="opacity-60">(Div)</span>
                                        @elseif($step->role)
                                            {{ $step->role->name }}
                                        @else
                                            —
                                        @endif
                                        @if($step->requires_director)
                                            <span class="text-red-400 font-normal">(Dir)</span>
                                        @endif
                                    </div>
                                @endforeach
                                <svg class="w-4 h-4 text-muted-foreground/40 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span class="badge-success">Selesai</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground py-6 text-center">Belum ada alur persetujuan.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <script>
        function flowBuilder() {
            return {
                steps: [{ role_id: '', requires_director: false }],
                addStep() { this.steps.push({ role_id: '', requires_director: false }); },
                removeStep(index) { if (this.steps.length > 1) this.steps.splice(index, 1); }
            }
        }
    </script>
</x-app-layout>