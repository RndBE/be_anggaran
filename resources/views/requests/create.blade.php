<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">{{ __('Create New Request') }}</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Buat pengajuan anggaran atau reimbursement baru</p>
        </div>
    </x-slot>

    <div class="py-6" x-data="requestForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="alert-destructive mb-4">
                    <p class="font-semibold mb-1">Ada beberapa masalah:</p>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <!-- Basic Information -->
                    <div class="card p-6 space-y-4">
                        <div>
                            <h3 class="card-title">Basic Info</h3>
                            <p class="card-description mt-1">Informasi dasar pengajuan</p>
                        </div>
                        <div class="separator"></div>

                        <div class="form-group">
                            <x-input-label for="type">Type of Request</x-input-label>
                            <select name="type" id="type" x-model="requestType" class="select-input" required>
                                <option value="budget">Pengajuan Anggaran (Budget)</option>
                                <option value="reimbursement">Reimbursement (Akomodasi/Entertain)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <x-input-label for="title">Request Title</x-input-label>
                            <x-text-input id="title" type="text" name="title"
                                placeholder="e.g. Perjalanan Dinas Surabaya" required value="{{ old('title') }}" />
                        </div>

                        <div class="form-group">
                            <x-input-label for="client_code_id">Client Code</x-input-label>
                            <select name="client_code_id" id="client_code_id" class="select-input" required>
                                <option value="">-- Select Client Code --</option>
                                @foreach($clientCodes as $client)
                                    <option value="{{ $client->id }}">{{ $client->prefix }}-{{ $client->instansi_singkat }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-destructive italic">Do not type raw client names in descriptions!</p>
                        </div>

                        <div class="form-group">
                            <x-input-label for="description">Description / Goal</x-input-label>
                            <textarea name="description" id="description" rows="3" class="textarea"
                                placeholder="Enter purpose of visit or goal">{{ old('description') }}</textarea>
                        </div>

                        <!-- Surat Tugas Number -->
                        <div class="form-group" x-data="suratTugasPreview()">
                            <x-input-label>Surat Tugas</x-input-label>
                            <div class="grid grid-cols-2 gap-3 mt-1.5">
                                <div>
                                    <label class="label text-xs">Nomor Urut</label>
                                    <input type="number" name="surat_tugas_urut" x-model="urut" @input="generate()"
                                        class="input w-full" min="1" placeholder="Contoh: 1"
                                        value="{{ old('surat_tugas_urut') }}">
                                </div>
                                <div>
                                    <label class="label text-xs">Tanggal Surat Tugas</label>
                                    <input type="date" name="surat_tugas_date" x-model="tanggal" @input="generate()"
                                        class="input w-full" value="{{ old('surat_tugas_date') }}">
                                </div>
                            </div>
                            <div x-show="preview"
                                class="mt-2 px-3 py-2 bg-primary/5 rounded-md border border-primary/20">
                                <p class="text-xs text-muted-foreground">Nomor Surat Tugas:</p>
                                <p class="text-sm font-bold text-primary" x-text="preview"></p>
                            </div>
                        </div>

                        <!-- Surat Tugas File Upload -->
                        <div class="form-group">
                            <x-input-label for="surat_tugas">
                                File Surat Tugas
                                <span class="font-normal text-muted-foreground text-xs ml-1">(PDF / JPG / PNG, maks 5
                                    MB)</span>
                            </x-input-label>
                            <div class="flex items-center gap-3 border-2 border-dashed border-border rounded-lg p-4 bg-muted/30 hover:bg-muted/50 transition-colors cursor-pointer"
                                onclick="document.getElementById('surat_tugas').click()">
                                <svg class="w-8 h-8 text-muted-foreground shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-muted-foreground" id="surat_tugas_label">Klik untuk pilih
                                        file surat tugas…</p>
                                </div>
                            </div>
                            <input type="file" id="surat_tugas" name="surat_tugas" accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                                onchange="document.getElementById('surat_tugas_label').textContent = this.files[0]?.name ?? 'Klik untuk pilih file surat tugas…'">
                            @error('surat_tugas')
                                <x-input-error :messages="[$message]" />
                            @enderror
                        </div>

                        <!-- Peserta Dinas -->
                        <div class="form-group">
                            <x-input-label>
                                👥 Peserta Dinas
                                <span class="text-muted-foreground font-normal text-xs ml-1">(opsional)</span>
                            </x-input-label>
                            <select id="participants-select" name="participants[]" multiple
                                placeholder="Cari nama karyawan…" class="w-full">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('participants.*')
                                <x-input-error :messages="[$message]" />
                            @enderror
                        </div>
                    </div>

                    <!-- Items Builder -->
                    <div class="card p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="card-title">Request Items</h3>
                                <p class="card-description mt-1">Tambahkan item pengeluaran</p>
                            </div>
                            <button type="button" @click="addItem()" class="btn-default btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add Item
                            </button>
                        </div>
                        <div class="separator mb-4"></div>

                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-muted/30 p-4 mb-3 rounded-lg border border-border relative">
                                <button type="button" @click="removeItem(index)"
                                    class="absolute top-3 right-3 text-muted-foreground hover:text-destructive transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div class="form-group">
                                        <label class="label">Item Type</label>
                                        <select x-bind:name="`items[${index}][type]`" x-model="item.type"
                                            @change="onTypeChange(item)" class="select-input" required>
                                            <option value="transport">Transport (Toll, Travel)</option>
                                            <option value="hotel">Hotel</option>
                                            <option value="meal_customer">Meal Customer</option>
                                            <option value="entertain">Entertain (Dir. Only)</option>
                                            <option value="lumpsum">Uang Makan / Lumpsum</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="label">Amount (Rp)</label>
                                        <input type="number" x-bind:name="`items[${index}][amount]`"
                                            x-model="item.amount" :readonly="item.type === 'lumpsum'"
                                            :class="item.type === 'lumpsum' ? 'input bg-muted cursor-not-allowed' : 'input'"
                                            min="0" required>
                                        <p x-show="item.type === 'lumpsum'" class="text-xs text-primary mt-0.5">Otomatis
                                            terhitung ↓</p>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="label">Details (e.g. from A to B)</label>
                                    <input type="text" x-bind:name="`items[${index}][description]`"
                                        x-model="item.description" class="input" required>
                                </div>

                                <!-- Lumpsum Sub-form -->
                                <div x-show="item.type === 'lumpsum'"
                                    class="mt-3 p-4 bg-primary/5 rounded-lg border border-primary/20 space-y-3">
                                    <p class="text-xs font-semibold text-primary">🍽️ Kalkulasi Uang Makan</p>
                                    <div class="form-group">
                                        <label class="label">Zona Perjalanan</label>
                                        <select x-bind:name="`items[${index}][travel_zone_id]`"
                                            x-model="item.travel_zone_id" @change="calcLumpsum(item)"
                                            class="select-input">
                                            <option value="">— Pilih zona —</option>
                                            @foreach($travelZones as $zone)
                                                <option value="{{ $zone->id }}" data-rate="{{ $zone->meal_allowance }}">
                                                    Zona {{ $zone->zone }} — {{ $zone->name }} (Rp
                                                    {{ number_format($zone->meal_allowance, 0, ',', '.') }}/hari)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="form-group">
                                            <label class="label">Jumlah Orang</label>
                                            <input type="number" x-bind:name="`items[${index}][person_count]`"
                                                x-model="item.person_count" @input="calcLumpsum(item)" min="1"
                                                placeholder="1" class="input">
                                        </div>
                                        <div class="form-group">
                                            <label class="label">Jumlah Hari</label>
                                            <input type="number" x-bind:name="`items[${index}][day_count]`"
                                                x-model="item.day_count" @input="calcLumpsum(item)" min="1"
                                                placeholder="1" class="input">
                                        </div>
                                    </div>
                                    <div x-show="item.amount > 0"
                                        class="flex items-center justify-between bg-card rounded-md p-3 border border-border">
                                        <span class="text-xs text-muted-foreground">
                                            Rp <span x-text="formatZoneRate(item)"></span>/hari
                                            × <span x-text="item.person_count || 1"></span> orang
                                            × <span x-text="item.day_count || 1"></span> hari
                                        </span>
                                        <span class="font-bold text-primary text-sm">
                                            = Rp <span
                                                x-text="new Intl.NumberFormat('id-ID').format(item.amount)"></span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Attachment for reimbursement -->
                                <div x-show="requestType === 'reimbursement'" class="mt-3">
                                    <label class="label mb-1 block">Evidence (Struk Asli, e-Toll, etc.)</label>
                                    <input type="file" x-bind:name="`items[${index}][attachment]`"
                                        class="input text-xs">
                                </div>
                            </div>
                        </template>

                        <div x-show="items.length === 0" class="text-center py-8 text-muted-foreground">
                            <svg class="w-10 h-10 mx-auto mb-2 text-muted" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            <p class="text-sm">No items added yet. Click "Add Item" to start.</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-border flex justify-between items-center">
                            <span class="text-sm font-medium text-muted-foreground">Total</span>
                            <span class="text-2xl font-bold text-primary" x-text="'Rp ' + calculateTotal()"></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('requests.index') }}" class="btn-outline">Cancel</a>
                    <button type="submit" class="btn-default">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('requestForm', () => ({
                requestType: 'budget',
                travelZones: @json($travelZones),
                items: [
                    { type: 'transport', amount: 0, description: '', travel_zone_id: '', person_count: 1, day_count: 1 }
                ],
                addItem() {
                    this.items.push({ type: 'transport', amount: 0, description: '', travel_zone_id: '', person_count: 1, day_count: 1 });
                },
                removeItem(index) { this.items.splice(index, 1); },
                onTypeChange(item) {
                    if (item.type === 'lumpsum') {
                        item.travel_zone_id = ''; item.person_count = 1; item.day_count = 1; item.amount = 0;
                    }
                },
                calcLumpsum(item) {
                    if (item.type !== 'lumpsum') return;
                    const zone = this.travelZones.find(z => z.id == item.travel_zone_id);
                    const rate = zone ? parseFloat(zone.meal_allowance) : 0;
                    item.amount = rate * (parseInt(item.person_count) || 1) * (parseInt(item.day_count) || 1);
                },
                formatZoneRate(item) {
                    const zone = this.travelZones.find(z => z.id == item.travel_zone_id);
                    return new Intl.NumberFormat('id-ID').format(zone ? parseFloat(zone.meal_allowance) : 0);
                },
                calculateTotal() {
                    return new Intl.NumberFormat('id-ID').format(this.items.reduce((s, i) => s + (Number(i.amount) || 0), 0));
                }
            }));

            Alpine.data('suratTugasPreview', () => ({
                urut: '',
                tanggal: '',
                preview: '',
                generate() {
                    if (!this.urut || !this.tanggal) { this.preview = ''; return; }
                    const roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                    const d = new Date(this.tanggal);
                    const no = String(this.urut).padStart(3, '0');
                    this.preview = `${no}/ST-ATC/${roman[d.getMonth()]}/${d.getFullYear()}`;
                }
            }));
        });
    </script>

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            new TomSelect('#participants-select', {
                plugins: ['remove_button', 'clear_button'],
                placeholder: 'Cari nama karyawan…',
                maxItems: null,
                create: false,
                sortField: { field: 'text', direction: 'asc' },
            });
        });
    </script>
</x-app-layout>