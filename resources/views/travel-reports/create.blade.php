<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('travel-reports.index') }}" class="text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-foreground">Buat Laporan Hasil Perjalanan</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Isi data LHP perjalanan dinas luar kota</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="alert-destructive mb-4">
                    <ul class="list-disc pl-4 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('travel-reports.store') }}" enctype="multipart/form-data" id="lhpForm">
                @csrf

                {{-- 1. Link to Request --}}
                <div class="card p-6 mb-5">
                    <h3 class="card-title mb-4">📋 Request Dinas Terkait</h3>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1.5">Pilih Request Dinas (Opsional)</label>
                        <select name="request_id" id="requestSelect"
                            class="input w-full"
                            onchange="if(this.value) window.location.href='{{ route('travel-reports.create') }}?request_id=' + this.value">
                            <option value="">— Tanpa request / Buat manual —</option>
                            @foreach($availableRequests as $req)
                                <option value="{{ $req->id }}" {{ ($selectedRequest && $selectedRequest->id == $req->id) ? 'selected' : '' }}>
                                    {{ $req->title }} — {{ $req->clientCode ? $req->clientCode->prefix . '-' . $req->clientCode->instansi_singkat : '' }}
                                    (Rp {{ number_format($req->total_amount, 0, ',', '.') }})
                                    {{ $req->surat_tugas_no ? '— ST: ' . $req->surat_tugas_no : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-muted-foreground mt-1">Pilih request yang sudah di-approve untuk auto-fill data</p>
                    </div>
                </div>

                {{-- 2. Identitas Perjalanan Dinas --}}
                <div class="card p-6 mb-5">
                    <h3 class="card-title mb-4">🆔 Identitas Perjalanan Dinas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Kota Tujuan <span class="text-destructive">*</span></label>
                            <input type="text" name="destination_city" class="input w-full"
                                value="{{ old('destination_city', $selectedRequest->description ?? '') }}" required
                                placeholder="Contoh: Jakarta">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Nomor Surat Tugas</label>
                            <input type="text" name="surat_tugas_no" class="input w-full {{ $selectedRequest && $selectedRequest->surat_tugas_no ? 'bg-muted' : '' }}"
                                value="{{ old('surat_tugas_no', $selectedRequest->surat_tugas_no ?? '') }}"
                                placeholder="Contoh: 001/ST-ATC/III/2026"
                                {{ $selectedRequest && $selectedRequest->surat_tugas_no ? 'readonly' : '' }}>
                            @if($selectedRequest && $selectedRequest->surat_tugas_no)
                                <p class="text-xs text-primary mt-1">✓ Otomatis dari request</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Tanggal Keberangkatan <span class="text-destructive">*</span></label>
                            <div class="date-input-wrapper">
                                <input type="text" name="departure_date" data-datepicker
                                    class="flatpickr-input w-full"
                                    value="{{ old('departure_date') }}"
                                    placeholder="Pilih tanggal…" required readonly>
                                <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Tanggal Kepulangan <span class="text-destructive">*</span></label>
                            <div class="date-input-wrapper">
                                <input type="text" name="return_date" data-datepicker
                                    class="flatpickr-input w-full"
                                    value="{{ old('return_date') }}"
                                    placeholder="Pilih tanggal…" required readonly>
                                <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Tanggal Surat Tugas</label>
                            <div class="date-input-wrapper">
                                <input type="text" name="surat_tugas_date" data-datepicker
                                    class="flatpickr-input w-full {{ $selectedRequest && $selectedRequest->surat_tugas_date ? 'bg-muted' : '' }}"
                                    value="{{ old('surat_tugas_date', $selectedRequest->surat_tugas_date ?? '') }}"
                                    placeholder="Pilih tanggal…"
                                    {{ $selectedRequest && $selectedRequest->surat_tugas_date ? 'readonly' : '' }}>
                                <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @if($selectedRequest && $selectedRequest->surat_tugas_date)
                                <p class="text-xs text-primary mt-1">✓ Otomatis dari request</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 3. Maksud dan Tujuan --}}
                <div class="card p-6 mb-5">
                    <h3 class="card-title mb-4">🎯 Maksud dan Tujuan Perjalanan Dinas</h3>
                    <div>
                        <label class="block text-sm font-medium text-foreground mb-1.5">Tujuan Perjalanan <span class="text-destructive">*</span></label>
                        <textarea name="purpose" class="input w-full" rows="3" required
                            placeholder="Jelaskan tujuan perjalanan dinas secara rinci...">{{ old('purpose', $selectedRequest->title ?? '') }}</textarea>
                    </div>
                </div>

                {{-- 4. GROUPED ACTIVITIES (Pelaksanaan + Hasil + Kendala + Kesimpulan + Dokumentasi per kegiatan) --}}
                <div id="activitiesContainer">
                    <div class="activity-block card mb-5 overflow-hidden" data-index="0">
                        <div class="px-6 py-4 bg-primary/5 border-b border-border flex items-center justify-between">
                            <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold activity-number">1</span>
                                Kegiatan 1
                            </h3>
                            <button type="button" onclick="removeActivityBlock(this)" class="btn-destructive btn-sm activity-remove-btn" style="display:none;">
                                ✕ Hapus
                            </button>
                        </div>
                        <div class="p-6 space-y-5">
                            {{-- Tanggal & Deskripsi --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Tanggal <span class="text-destructive">*</span></label>
                                    <div class="date-input-wrapper">
                                        <input type="text" name="activities[0][date]" data-datepicker
                                            class="flatpickr-input w-full" placeholder="Pilih tanggal…" required readonly>
                                        <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Pelaksanaan Kegiatan <span class="text-destructive">*</span></label>
                                    <textarea name="activities[0][description]" class="input w-full" rows="2" required
                                        placeholder="Deskripsi kegiatan pada tanggal ini..."></textarea>
                                </div>
                            </div>

                            {{-- Hasil --}}
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">✅ Hasil yang Dicapai</label>
                                <div class="results-container space-y-2">
                                    <div class="result-row flex gap-2 items-center">
                                        <span class="text-xs font-medium text-muted-foreground w-5 shrink-0 result-num">1.</span>
                                        <input type="text" name="activities[0][results][]" class="input flex-1 text-sm" placeholder="Hasil yang dicapai...">
                                        <button type="button" onclick="removeSubRow(this, 'result')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 sub-remove" style="display:none;">✕</button>
                                    </div>
                                </div>
                                <button type="button" onclick="addSubRow(this, 'result', 0)" class="text-xs text-primary hover:underline mt-1.5">+ Tambah Hasil</button>
                            </div>

                            {{-- Permasalahan --}}
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">⚠️ Permasalahan / Kendala</label>
                                <textarea name="activities[0][issues]" class="input w-full text-sm" rows="2"
                                    placeholder="Kosongkan jika tidak ada kendala..."></textarea>
                            </div>

                            {{-- Kesimpulan per kegiatan --}}
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">📝 Kesimpulan Kegiatan</label>
                                <textarea name="activities[0][conclusion]" class="input w-full text-sm" rows="2"
                                    placeholder="Kesimpulan untuk kegiatan ini..."></textarea>
                            </div>

                            {{-- Dokumentasi --}}
                            <div>
                                <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">📸 Dokumentasi</label>
                                <div class="docs-container space-y-2">
                                    <div class="doc-row flex gap-2 items-start">
                                        <div class="flex-1">
                                            <input type="file" name="activities[0][documents][]" class="input w-full text-sm" accept=".jpg,.jpeg,.png,.pdf">
                                        </div>
                                        <div class="w-40 shrink-0">
                                            <input type="text" name="activities[0][document_captions][]" class="input w-full text-sm" placeholder="Keterangan">
                                        </div>
                                        <button type="button" onclick="removeSubRow(this, 'doc')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 mt-2 sub-remove" style="display:none;">✕</button>
                                    </div>
                                </div>
                                <button type="button" onclick="addSubRow(this, 'doc', 0)" class="text-xs text-primary hover:underline mt-1.5">+ Tambah Foto</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <button type="button" onclick="addActivityBlock()" class="btn-outline w-full py-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Kegiatan Baru
                    </button>
                </div>

                {{-- 5. Kesimpulan & Rekomendasi Umum --}}
                <div class="card p-6 mb-5">
                    <h3 class="card-title mb-4">📋 Kesimpulan & Rekomendasi Umum</h3>
                    <p class="text-xs text-muted-foreground mb-4">Kesimpulan dan rekomendasi secara keseluruhan dari perjalanan dinas.</p>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Kesimpulan <span class="text-destructive">*</span></label>
                            <textarea name="conclusion" class="input w-full" rows="3" required
                                placeholder="Tuliskan kesimpulan keseluruhan dari perjalanan dinas...">{{ old('conclusion') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-foreground mb-1.5">Rekomendasi Tindak Lanjut</label>
                            <div id="recommendationsContainer">
                                <div class="recommendation-row flex gap-2 mb-2 items-center">
                                    <span class="text-sm font-medium text-muted-foreground w-5 shrink-0 rec-num">1.</span>
                                    <input type="text" name="recommendations[]" class="input flex-1"
                                        value="{{ old('recommendations.0') }}"
                                        placeholder="Rekomendasi tindak lanjut...">
                                    <button type="button" onclick="removeRecommendation(this)" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 rec-remove" style="display:none;">✕</button>
                                </div>
                            </div>
                            <button type="button" onclick="addRecommendation()" class="text-xs text-primary hover:underline mt-1">+ Tambah Rekomendasi</button>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('travel-reports.index') }}" class="btn-outline">Batal</a>
                    <button type="submit" class="btn-default">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan LHP
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let activityIndex = 1;

        // =============================================
        // ACTIVITY BLOCKS
        // =============================================
        function addActivityBlock() {
            const container = document.getElementById('activitiesContainer');
            const idx = activityIndex;
            const num = container.querySelectorAll('.activity-block').length + 1;

            const block = document.createElement('div');
            block.className = 'activity-block card mb-5 overflow-hidden';
            block.dataset.index = idx;
            block.innerHTML = `
                <div class="px-6 py-4 bg-primary/5 border-b border-border flex items-center justify-between">
                    <h3 class="text-sm font-bold text-primary flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold activity-number">${num}</span>
                        Kegiatan ${num}
                    </h3>
                    <button type="button" onclick="removeActivityBlock(this)" class="btn-destructive btn-sm activity-remove-btn">
                        ✕ Hapus
                    </button>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Tanggal <span class="text-destructive">*</span></label>
                            <div class="date-input-wrapper">
                                <input type="text" name="activities[${idx}][date]" data-datepicker
                                    class="flatpickr-input w-full" placeholder="Pilih tanggal…" required readonly>
                                <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">Pelaksanaan Kegiatan <span class="text-destructive">*</span></label>
                            <textarea name="activities[${idx}][description]" class="input w-full" rows="2" required
                                placeholder="Deskripsi kegiatan pada tanggal ini..."></textarea>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">✅ Hasil yang Dicapai</label>
                        <div class="results-container space-y-2">
                            <div class="result-row flex gap-2 items-center">
                                <span class="text-xs font-medium text-muted-foreground w-5 shrink-0 result-num">1.</span>
                                <input type="text" name="activities[${idx}][results][]" class="input flex-1 text-sm" placeholder="Hasil yang dicapai...">
                                <button type="button" onclick="removeSubRow(this, 'result')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 sub-remove" style="display:none;">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addSubRow(this, 'result', ${idx})" class="text-xs text-primary hover:underline mt-1.5">+ Tambah Hasil</button>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">⚠️ Permasalahan / Kendala</label>
                        <textarea name="activities[${idx}][issues]" class="input w-full text-sm" rows="2"
                            placeholder="Kosongkan jika tidak ada kendala..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">📝 Kesimpulan Kegiatan</label>
                        <textarea name="activities[${idx}][conclusion]" class="input w-full text-sm" rows="2"
                            placeholder="Kesimpulan untuk kegiatan ini..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1.5">📸 Dokumentasi</label>
                        <div class="docs-container space-y-2">
                            <div class="doc-row flex gap-2 items-start">
                                <div class="flex-1">
                                    <input type="file" name="activities[${idx}][documents][]" class="input w-full text-sm" accept=".jpg,.jpeg,.png,.pdf">
                                </div>
                                <div class="w-40 shrink-0">
                                    <input type="text" name="activities[${idx}][document_captions][]" class="input w-full text-sm" placeholder="Keterangan">
                                </div>
                                <button type="button" onclick="removeSubRow(this, 'doc')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 mt-2 sub-remove" style="display:none;">✕</button>
                            </div>
                        </div>
                        <button type="button" onclick="addSubRow(this, 'doc', ${idx})" class="text-xs text-primary hover:underline mt-1.5">+ Tambah Foto</button>
                    </div>
                </div>
            `;
            container.appendChild(block);
            activityIndex++;
            updateActivityNumbers();
            // Init Flatpickr on the new block's date input
            initFlatpickr(block);
        }

        function removeActivityBlock(btn) {
            const block = btn.closest('.activity-block');
            block.remove();
            updateActivityNumbers();
        }

        function updateActivityNumbers() {
            const blocks = document.querySelectorAll('#activitiesContainer .activity-block');
            blocks.forEach((block, i) => {
                const numEl = block.querySelector('.activity-number');
                const titleEl = numEl.parentElement;
                numEl.textContent = i + 1;
                titleEl.childNodes[titleEl.childNodes.length - 1].textContent = ` Kegiatan ${i + 1}`;

                // Show/hide remove buttons
                const removeBtn = block.querySelector('.activity-remove-btn');
                if (removeBtn) {
                    removeBtn.style.display = blocks.length > 1 ? '' : 'none';
                }
            });
        }

        // =============================================
        // SUB-ROWS (Results, Docs)
        // =============================================
        function addSubRow(addBtn, type, actIdx) {
            const container = addBtn.previousElementSibling;
            const actBlock = addBtn.closest('.activity-block');
            const idx = actBlock ? actBlock.dataset.index : actIdx;

            if (type === 'result') {
                const count = container.querySelectorAll('.result-row').length + 1;
                const row = document.createElement('div');
                row.className = 'result-row flex gap-2 items-center';
                row.innerHTML = `
                    <span class="text-xs font-medium text-muted-foreground w-5 shrink-0 result-num">${count}.</span>
                    <input type="text" name="activities[${idx}][results][]" class="input flex-1 text-sm" placeholder="Hasil yang dicapai...">
                    <button type="button" onclick="removeSubRow(this, 'result')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 sub-remove">✕</button>
                `;
                container.appendChild(row);
            } else if (type === 'doc') {
                const row = document.createElement('div');
                row.className = 'doc-row flex gap-2 items-start';
                row.innerHTML = `
                    <div class="flex-1">
                        <input type="file" name="activities[${idx}][documents][]" class="input w-full text-sm" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="w-40 shrink-0">
                        <input type="text" name="activities[${idx}][document_captions][]" class="input w-full text-sm" placeholder="Keterangan">
                    </div>
                    <button type="button" onclick="removeSubRow(this, 'doc')" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 mt-2 sub-remove">✕</button>
                `;
                container.appendChild(row);
            }
            updateSubRemoveButtons(container, type);
        }

        function removeSubRow(btn, type) {
            const container = btn.closest(type === 'result' ? '.results-container' : '.docs-container');
            btn.closest(type === 'result' ? '.result-row' : '.doc-row').remove();
            if (type === 'result') {
                container.querySelectorAll('.result-row').forEach((row, i) => {
                    row.querySelector('.result-num').textContent = (i + 1) + '.';
                });
            }
            updateSubRemoveButtons(container, type);
        }

        function updateSubRemoveButtons(container, type) {
            const rows = container.querySelectorAll(type === 'result' ? '.result-row' : '.doc-row');
            rows.forEach(row => {
                const btn = row.querySelector('.sub-remove');
                if (btn) {
                    btn.style.display = rows.length > 1 ? '' : 'none';
                }
            });
        }

        // =============================================
        // RECOMMENDATIONS
        // =============================================
        function addRecommendation() {
            const container = document.getElementById('recommendationsContainer');
            const count = container.querySelectorAll('.recommendation-row').length + 1;
            const row = document.createElement('div');
            row.className = 'recommendation-row flex gap-2 mb-2 items-center';
            row.innerHTML = `
                <span class="text-sm font-medium text-muted-foreground w-5 shrink-0 rec-num">${count}.</span>
                <input type="text" name="recommendations[]" class="input flex-1" placeholder="Rekomendasi tindak lanjut...">
                <button type="button" onclick="removeRecommendation(this)" class="text-destructive hover:text-destructive/80 text-sm font-bold shrink-0 rec-remove">✕</button>
            `;
            container.appendChild(row);
            updateRecRemoveButtons();
        }

        function removeRecommendation(btn) {
            btn.closest('.recommendation-row').remove();
            const container = document.getElementById('recommendationsContainer');
            container.querySelectorAll('.recommendation-row').forEach((row, i) => {
                row.querySelector('.rec-num').textContent = (i + 1) + '.';
            });
            updateRecRemoveButtons();
        }

        function updateRecRemoveButtons() {
            const container = document.getElementById('recommendationsContainer');
            const rows = container.querySelectorAll('.recommendation-row');
            rows.forEach(row => {
                const btn = row.querySelector('.rec-remove');
                if (btn) btn.style.display = rows.length > 1 ? '' : 'none';
            });
        }

        // Init
        updateActivityNumbers();
        updateRecRemoveButtons();
    </script>
    @endpush
</x-app-layout>
