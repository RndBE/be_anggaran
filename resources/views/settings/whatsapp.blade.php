<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                WhatsApp Gateway
            </h2>
            <span class="text-xs text-gray-400 font-mono bg-gray-100 px-2 py-1 rounded">
                Session: <strong>{{ $session }}</strong> · {{ $baseUrl }}
            </span>
        </div>
    </x-slot>

    <div class="py-6" x-data="waPanel()" x-init="init()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}</div>
            @endif

            {{-- ── Status Card ─────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Status Koneksi</h3>
                    <div class="flex items-center gap-3">
                        {{-- Live status badge --}}
                        <div class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full transition-all"
                            :class="{
                                'bg-green-100 text-green-700': status === 'CONNECTED',
                                'bg-yellow-100 text-yellow-700': status === 'SCAN_QR_CODE',
                                'bg-red-100 text-red-700': ['DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status),
                                'bg-blue-100 text-blue-700': !['CONNECTED','SCAN_QR_CODE','DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status)
                            }">
                            <span class="w-2 h-2 rounded-full inline-block animate-pulse" :class="{
                                    'bg-green-500': status === 'CONNECTED',
                                    'bg-yellow-500': status === 'SCAN_QR_CODE',
                                    'bg-red-500': ['DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status),
                                    'bg-blue-500': !['CONNECTED','SCAN_QR_CODE','DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status)
                                }"></span>
                            <span x-text="statusLabel()"></span>
                        </div>
                        <span class="text-xs text-gray-400" x-text="'Updated ' + timeAgo"></span>
                    </div>
                </div>

                <div class="p-6">
                    {{-- Connected state --}}
                    <div x-show="status === 'CONNECTED'" class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-green-100 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">WhatsApp Terhubung ✅</p>
                            <p class="text-sm text-gray-500 mt-0.5">Session <strong>{{ $session }}</strong> aktif dan
                                siap mengirim pesan.</p>
                            <p class="text-xs text-gray-400 mt-1" x-text="phoneInfo"></p>
                        </div>
                    </div>

                    {{-- QR Code state --}}
                    <div x-show="status === 'SCAN_QR_CODE'" class="text-center">
                        <p class="text-sm text-amber-700 font-semibold mb-4">
                            📱 Scan QR Code dengan WhatsApp mobile → Perangkat Tertaut → Tautkan Perangkat
                        </p>
                        <div class="inline-block p-4 bg-white border-2 border-amber-300 rounded-2xl shadow-md">
                            <img :src="qrImageUrl + '&t=' + Date.now()" alt="QR Code WhatsApp"
                                class="w-56 h-56 object-contain" @error="qrError = true">
                        </div>
                        <p class="text-xs text-gray-400 mt-3">QR code akan refresh otomatis setiap 15 detik</p>
                    </div>

                    {{-- Disconnected / Unreachable state --}}
                    <div x-show="['DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status)"
                        class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M6.343 17.657a9 9 0 010-12.728M9.172 15.172a5 5 0 010-7.072M12 12h.01" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Tidak Terhubung</p>
                            <p class="text-sm text-gray-500 mt-0.5">WhatsApp belum terkoneksi. Klik <strong>Mulai
                                    Session</strong> untuk memulai.</p>
                        </div>
                    </div>

                    {{-- Other states (INITIALIZING, LOADING etc.) --}}
                    <div x-show="!['CONNECTED','SCAN_QR_CODE','DISCONNECTED','UNREACHABLE','UNKNOWN'].includes(status)"
                        class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Memproses…</p>
                            <p class="text-sm text-gray-500 mt-0.5" x-text="'Status: ' + status"></p>
                        </div>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="px-6 pb-5 flex items-center gap-3">
                    <button @click="startSession()" :disabled="loading || status === 'CONNECTED'"
                        class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Mulai Session
                    </button>
                    <button @click="terminateSession()"
                        :disabled="loading || status === 'DISCONNECTED' || status === 'UNREACHABLE'"
                        class="flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-red-500 rounded-xl hover:bg-red-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Putus Koneksi
                    </button>
                    <button @click="poll()"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">
                        ↻ Refresh
                    </button>

                    {{-- spinner --}}
                    <svg x-show="loading" class="w-5 h-5 text-gray-400 animate-spin ml-1" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>

                    <span x-show="actionMsg" x-text="actionMsg"
                        class="text-xs text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded"></span>
                </div>
            </div>

            {{-- ── Test Send Card ────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Test Kirim Pesan</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Hanya tersedia saat status <span
                            class="text-green-600 font-semibold">CONNECTED</span></p>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                        <input x-model="testPhone" type="text" placeholder="08123456789 atau 6281234567890"
                            class="w-full rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-400 mt-1">Format lokal (08xx) atau internasional (628xx)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                        <textarea x-model="testMessage" rows="3"
                            placeholder="Halo, ini pesan test dari sistem BE Anggaran…"
                            class="w-full rounded-xl border-gray-300 text-sm focus:ring-green-500 focus:border-green-500"></textarea>
                    </div>
                    <div class="flex items-center gap-3">
                        <button @click="sendTest()" :disabled="loading || status !== 'CONNECTED'"
                            class="px-5 py-2 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all shadow-sm">
                            📤 Kirim Test
                        </button>
                        <span x-show="testResult" x-text="testResult"
                            :class="testOk ? 'text-green-600' : 'text-red-500'" class="text-sm font-medium"></span>
                    </div>
                </div>
            </div>

            {{-- ── Info Card ─────────────────────────────────────────────── --}}
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-5 text-xs text-gray-500 space-y-1">
                <p class="font-semibold text-gray-600 mb-2">ℹ️ Informasi Gateway</p>
                <p>• Server: <code class="bg-white px-1 rounded border">{{ $baseUrl }}</code></p>
                <p>• Session: <code class="bg-white px-1 rounded border">{{ $session }}</code></p>
                <p>• Auto-refresh status setiap <strong>10 detik</strong></p>
                <p>• QR Code auto-refresh setiap <strong>15 detik</strong> saat menunggu scan</p>
            </div>

        </div>
    </div>

    <script>
        function waPanel() {
            return {
                status: @json($status),
                statusData: @json($statusData),
                qrImageUrl: @json($qrImageUrl),
                loading: false,
                timeAgo: 'just now',
                phoneInfo: '',
                actionMsg: '',
                testPhone: '',
                testMessage: '',
                testResult: '',
                testOk: false,
                _pollTimer: null,
                _qrTimer: null,
                _lastPoll: null,

                init() {
                    this.updateTimeAgo();
                    this.poll();  // langsung poll saat halaman selesai load
                    this._pollTimer = setInterval(() => this.poll(), 10000);
                    setInterval(() => this.updateTimeAgo(), 5000);
                },

                statusLabel() {
                    const map = {
                        CONNECTED: 'Terhubung',
                        SCAN_QR_CODE: 'Scan QR Code',
                        DISCONNECTED: 'Terputus',
                        UNREACHABLE: 'Server Unreachable',
                        UNKNOWN: 'Tidak Diketahui',
                    };
                    return map[this.status] ?? this.status;
                },

                async poll() {
                    try {
                        const res = await fetch('{{ route("settings.whatsapp.status") }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await res.json();
                        this.status = data.status ?? 'UNKNOWN';
                        this.statusData = data;
                        this.phoneInfo = data.phone ?? '';
                        this._lastPoll = new Date();
                        this.timeAgo = 'just now';
                    } catch (e) {
                        this.status = 'UNREACHABLE';
                    }
                },

                updateTimeAgo() {
                    if (!this._lastPoll) return;
                    const diff = Math.round((new Date() - this._lastPoll) / 1000);
                    this.timeAgo = diff < 5 ? 'just now' : diff + 's ago';
                },

                async startSession() {
                    this.loading = true;
                    this.actionMsg = 'Memulai session…';
                    try {
                        await fetch('{{ route("settings.whatsapp.start") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        this.actionMsg = 'Session dimulai. Menunggu QR…';
                        setTimeout(() => this.poll(), 2000);
                    } catch (e) {
                        this.actionMsg = 'Gagal memulai session.';
                    } finally {
                        this.loading = false;
                    }
                },

                async terminateSession() {
                    if (!confirm('Yakin ingin memutus koneksi WhatsApp?')) return;
                    this.loading = true;
                    this.actionMsg = 'Memutus koneksi…';
                    try {
                        await fetch('{{ route("settings.whatsapp.terminate") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        this.actionMsg = 'Session diputus.';
                        setTimeout(() => this.poll(), 1000);
                    } catch (e) {
                        this.actionMsg = 'Gagal memutus session.';
                    } finally {
                        this.loading = false;
                    }
                },

                async sendTest() {
                    if (!this.testPhone || !this.testMessage) return;
                    this.loading = true;
                    this.testResult = '';
                    try {
                        const res = await fetch('{{ route("settings.whatsapp.test-send") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                phone: this.testPhone,
                                message: this.testMessage,
                            })
                        });
                        const data = await res.json();
                        this.testOk = data.ok ?? false;
                        this.testResult = data.ok ? '✅ Pesan terkirim!' : '❌ Gagal: ' + (data.error ?? 'Unknown error');
                    } catch (e) {
                        this.testOk = false;
                        this.testResult = '❌ ' + e.message;
                    } finally {
                        this.loading = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>