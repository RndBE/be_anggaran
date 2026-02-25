<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Request') }}
        </h2>
    </x-slot>

    <div class="py-5" x-data="requestForm()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong>Oops!</strong> There were some problems with your input.<br><br>
                            <ul class="list-disc pl-5 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 p-4 rounded border">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Basic Info</h3>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                                        Type of Request
                                    </label>
                                    <select name="type" id="type" x-model="requestType"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                        <option value="budget">Pengajuan Anggaran (Budget)</option>
                                        <option value="reimbursement">Reimbursement (Akomodasi/Entertain)</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                                        Request Title
                                    </label>
                                    <input type="text" name="title" id="title"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="e.g. Perjalanan Dinas Surabaya" required
                                        value="{{ old('title') }}">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="client_code_id">
                                        Client Code (Sensitive Data)
                                    </label>
                                    <select name="client_code_id" id="client_code_id"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                        <option value="">-- Select Client Code --</option>
                                        @foreach($clientCodes as $client)
                                            <option value="{{ $client->id }}">
                                                {{ $client->prefix }}-{{ $client->instansi_singkat }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-red-500 mt-1 italic">Do not type raw client names in
                                        descriptions!</p>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                                        Description / Goal
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="Enter purpose of visit or goal">{{ old('description') }}</textarea>
                                </div>

                                {{-- Surat Tugas --}}
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="surat_tugas">
                                        Surat Tugas
                                        <span class="font-normal text-gray-500 text-xs ml-1">(PDF / JPG / PNG, maks 5
                                            MB)</span>
                                    </label>
                                    <div class="flex items-center gap-3 border border-dashed border-gray-300 rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer"
                                        onclick="document.getElementById('surat_tugas').click()">
                                        <svg class="w-8 h-8 text-gray-400 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-500" id="surat_tugas_label">Klik untuk pilih
                                                file surat tugas…</p>
                                        </div>
                                    </div>
                                    <input type="file" id="surat_tugas" name="surat_tugas" accept=".pdf,.jpg,.jpeg,.png"
                                        class="hidden"
                                        onchange="document.getElementById('surat_tugas_label').textContent = this.files[0]?.name ?? 'Klik untuk pilih file surat tugas…'">
                                    @error('surat_tugas')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- ── Peserta Dinas ──────────────────────────── --}}
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    👥 Peserta Dinas
                                    <span class="text-gray-400 font-normal text-xs ml-1">(opsional – pilih karyawan yang ikut)</span>
                                </label>
                                <select id="participants-select" name="participants[]" multiple
                                    placeholder="Cari nama karyawan…"
                                    class="w-full">
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('participants.*')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Items Builder -->
                            <div class="bg-blue-50 p-4 rounded border border-blue-100">
                                <div class="flex justify-between items-center mb-4 border-b border-blue-200 pb-2">
                                    <h3 class="text-lg font-bold text-blue-800">Request Items</h3>
                                    <button type="button" @click="addItem()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        + Add Item
                                    </button>
                                </div>

                                <template x-for="(item, index) in items" :key="index">
                                    <div class="bg-white p-3 mb-3 rounded shadow-sm border border-gray-200 relative">
                                        <button type="button" @click="removeItem(index)"
                                            class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <div class="grid grid-cols-2 gap-2 mb-2">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Item
                                                    Type</label>
                                                <select x-bind:name="`items[${index}][type]`" x-model="item.type"
                                                    @change="onTypeChange(item)"
                                                    class="w-full text-sm rounded border-gray-300 p-1" required>
                                                    <option value="transport">Transport (Toll, Travel)</option>
                                                    <option value="hotel">Hotel</option>
                                                    <option value="meal_customer">Meal Customer</option>
                                                    <option value="entertain">Entertain (Dir. Only)</option>
                                                    <option value="lumpsum">Uang Makan / Lumpsum</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Amount
                                                    (Rp)</label>
                                                <input type="number" x-bind:name="`items[${index}][amount]`"
                                                    x-model="item.amount" :readonly="item.type === 'lumpsum'"
                                                    :class="item.type === 'lumpsum' ? 'bg-gray-100 cursor-not-allowed' : ''"
                                                    class="w-full text-sm rounded border-gray-300 p-1" min="0" required>
                                                <p x-show="item.type === 'lumpsum'"
                                                    class="text-xs text-blue-500 mt-0.5">Otomatis terhitung ↓</p>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Details (e.g. from
                                                A to B)</label>
                                            <input type="text" x-bind:name="`items[${index}][description]`"
                                                x-model="item.description"
                                                class="w-full text-sm rounded border-gray-300 p-1" required>
                                        </div>

                                        {{-- ── Lumpsum Sub-form ─────────────────────── --}}
                                        <div x-show="item.type === 'lumpsum'"
                                            class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200 space-y-3">
                                            <p class="text-xs font-bold text-blue-700 mb-2">🍽️ Kalkulasi Uang Makan</p>

                                            {{-- Zona --}}
                                            <div>
                                                <label class="block text-xs font-bold text-gray-700 mb-1">Zona
                                                    Perjalanan</label>
                                                <select x-bind:name="`items[${index}][travel_zone_id]`"
                                                    x-model="item.travel_zone_id" @change="calcLumpsum(item)"
                                                    class="w-full text-sm rounded border-gray-300 p-1">
                                                    <option value="">— Pilih zona —</option>
                                                    @foreach($travelZones as $zone)
                                                        <option value="{{ $zone->id }}"
                                                            data-rate="{{ $zone->meal_allowance }}">
                                                            Zona {{ $zone->zone }} — {{ $zone->name }}
                                                            (Rp
                                                            {{ number_format($zone->meal_allowance, 0, ',', '.') }}/hari)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- Orang & Hari --}}
                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah
                                                        Orang</label>
                                                    <input type="number" x-bind:name="`items[${index}][person_count]`"
                                                        x-model="item.person_count" @input="calcLumpsum(item)" min="1"
                                                        placeholder="1"
                                                        class="w-full text-sm rounded border-gray-300 p-1">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-gray-700 mb-1">Jumlah
                                                        Hari</label>
                                                    <input type="number" x-bind:name="`items[${index}][day_count]`"
                                                        x-model="item.day_count" @input="calcLumpsum(item)" min="1"
                                                        placeholder="1"
                                                        class="w-full text-sm rounded border-gray-300 p-1">
                                                </div>
                                            </div>

                                            {{-- Result preview --}}
                                            <div x-show="item.amount > 0"
                                                class="flex items-center justify-between bg-white rounded p-2 border border-blue-200">
                                                <span class="text-xs text-gray-500">
                                                    Rp <span x-text="formatZoneRate(item)"></span>/hari
                                                    × <span x-text="item.person_count || 1"></span> orang
                                                    × <span x-text="item.day_count || 1"></span> hari
                                                </span>
                                                <span class="font-bold text-blue-700 text-sm">
                                                    = Rp <span
                                                        x-text="new Intl.NumberFormat('id-ID').format(item.amount)"></span>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Only show attachment for reimbursement -->
                                        <div x-show="requestType === 'reimbursement'"
                                            class="mt-2 bg-gray-50 p-2 rounded">
                                            <label class="block text-xs font-bold text-gray-700 mb-1">Evidence (Struk
                                                Asli, e-Toll, etc.)</label>
                                            <input type="file" x-bind:name="`items[${index}][attachment]`"
                                                class="w-full text-xs">
                                        </div>
                                    </div>
                                </template>

                                <div x-show="items.length === 0" class="text-center p-4 text-gray-500 italic text-sm">
                                    No items added. Please add at least one request item.
                                </div>

                                <div class="mt-4 pt-3 border-t text-right">
                                    <span class="font-bold text-gray-700">Total: </span>
                                    <span class="text-xl font-bold text-blue-600"
                                        x-text="'Rp ' + calculateTotal()"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4 pt-4 border-t">
                            <a href="{{ route('requests.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                            <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine JS Script for Form -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('requestForm', () => ({
                requestType: 'budget',
                travelZones: @json($travelZones),
                items: [
                    { type: 'transport', amount: 0, description: '', travel_zone_id: '', person_count: 1, day_count: 1 }
                ],

                addItem() {
                    this.items.push({
                        type: 'transport', amount: 0, description: '',
                        travel_zone_id: '', person_count: 1, day_count: 1
                    });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                onTypeChange(item) {
                    if (item.type === 'lumpsum') {
                        item.travel_zone_id = '';
                        item.person_count = 1;
                        item.day_count = 1;
                        item.amount = 0;
                    }
                },

                calcLumpsum(item) {
                    if (item.type !== 'lumpsum') return;
                    const zone = this.travelZones.find(z => z.id == item.travel_zone_id);
                    const rate = zone ? parseFloat(zone.meal_allowance) : 0;
                    const orang = parseInt(item.person_count) || 1;
                    const hari = parseInt(item.day_count) || 1;
                    item.amount = rate * orang * hari;
                },

                formatZoneRate(item) {
                    const zone = this.travelZones.find(z => z.id == item.travel_zone_id);
                    const rate = zone ? parseFloat(zone.meal_allowance) : 0;
                    return new Intl.NumberFormat('id-ID').format(rate);
                },

                calculateTotal() {
                    const total = this.items.reduce((sum, item) => sum + (Number(item.amount) || 0), 0);
                    return new Intl.NumberFormat('id-ID').format(total);
                }
            }));
        });
    </script>

    {{-- Tom Select — searchable multi-select untuk peserta dinas --}}
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
                render: {
                    option: (data, escape) =>
                        `<div class="py-1 px-2 text-sm">${escape(data.text)}</div>`,
                    item: (data, escape) =>
                        `<div class="text-sm">${escape(data.text)}</div>`,
                }
            });
        });
    </script>

</x-app-layout>