<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">Selamat datang, <span class="font-medium text-indigo-600">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->division)
                        — <span class="text-gray-600">{{ Auth::user()->division->name }}</span>
                        @if(Auth::user()->level)
                            <span class="ml-1 text-xs font-semibold px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded">Level {{ Auth::user()->level }}</span>
                        @endif
                    @endif
                </p>
            </div>
            <p class="text-sm text-gray-400">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-3">

            
            @if($canManage)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($systemStats['total_requests']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Total Requests</p>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-amber-600">{{ number_format($systemStats['pending']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Menunggu Approval</p>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($systemStats['approved']) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Disetujui</p>
                    </div>
                </div>
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-blue-700 leading-tight">Rp {{ number_format($systemStats['approved_amount']/1000000, 1) }}Jt</p>
                        <p class="text-xs text-gray-500 mt-0.5">Total Disetujui</p>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Pipeline Requests</h3>
                    @php
                        $total = max($systemStats['total_requests'], 1);
                        $pipeline = [
                            ['label' => 'Pending', 'count' => $systemStats['pending'], 'color' => 'bg-amber-400', 'text' => 'text-amber-700'],
                            ['label' => 'Disetujui', 'count' => $systemStats['approved'], 'color' => 'bg-green-400', 'text' => 'text-green-700'],
                            ['label' => 'Ditolak', 'count' => $systemStats['rejected'], 'color' => 'bg-red-400', 'text' => 'text-red-700'],
                            ['label' => 'Revisi', 'count' => $systemStats['revision'], 'color' => 'bg-purple-400', 'text' => 'text-purple-700'],
                        ];
                    @endphp
                    
                    <div class="flex h-8 rounded-xl overflow-hidden gap-0.5 mb-4">
                        @foreach($pipeline as $p)
                            @php $pct = round($p['count'] / $total * 100); @endphp
                            @if($p['count'] > 0)
                                <div class="{{ $p['color'] }} flex items-center justify-center text-white text-xs font-bold transition-all"
                                    style="width: {{ $pct }}%" title="{{ $p['label'] }}: {{ $p['count'] }}">
                                    @if($pct > 10){{ $pct }}%@endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-4">
                        @foreach($pipeline as $p)
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $p['color'] }}"></span>
                                <span class="text-sm text-gray-600">{{ $p['label'] }}</span>
                                <span class="text-sm font-bold {{ $p['text'] }}">{{ $p['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Pengeluaran per Kategori</h3>
                    @php $maxSpend = $spendingByType->max('total') ?: 1; @endphp
                    <div class="space-y-3">
                        @forelse($spendingByType as $item)
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span class="capitalize font-medium">{{ str_replace('_', ' ', $item->type) }}</span>
                                    <span class="font-semibold text-gray-800">Rp {{ number_format($item->total/1000, 0) }}rb</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all"
                                        style="width: {{ round($item->total / $maxSpend * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 italic">Belum ada data pengeluaran.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-5">Tren Request — 6 Bulan Terakhir</h3>
                @php $maxCount = max($months->max('count'), 1); @endphp
                <div class="flex items-end gap-3 h-32">
                    @foreach($months as $key => $m)
                        @php
                            $barH = max(round(($m->count / $maxCount) * 100), 2);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $key)->locale('id')->isoFormat('MMM');
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group">
                            <span class="text-xs text-gray-500 font-semibold opacity-0 group-hover:opacity-100 transition-opacity">{{ $m->count }}</span>
                            <div class="w-full rounded-t-lg bg-gradient-to-t from-indigo-600 to-indigo-400 hover:from-indigo-700 hover:to-indigo-500 transition-colors cursor-default"
                                style="height: {{ $barH }}%"
                                title="{{ $label }}: {{ $m->count }} request (Rp {{ number_format($m->total/1000) }}rb)">
                            </div>
                            <span class="text-xs text-gray-500 capitalize">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Request Saya</h3>
                        <span class="text-2xl font-bold text-indigo-600">{{ $myCount }}</span>
                    </div>
                    @php
                        $statusConfig = [
                            'submitted'          => ['label' => 'Submitted', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                            'pending'            => ['label' => 'Pending', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                            'approved'           => ['label' => 'Disetujui', 'bg' => 'bg-green-100', 'text' => 'text-green-700'],
                            'rejected'           => ['label' => 'Ditolak', 'bg' => 'bg-red-100', 'text' => 'text-red-700'],
                            'revision_requested' => ['label' => 'Revisi', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($statusConfig as $status => $cfg)
                            @php $count = $myRequests[$status] ?? 0; @endphp
                            @if($count > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm {{ $cfg['text'] }} {{ $cfg['bg'] }} px-2 py-0.5 rounded-full font-medium">{{ $cfg['label'] }}</span>
                                    <span class="font-bold text-gray-800">{{ $count }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if($myTotal > 0)
                        <div class="mt-4 pt-3 border-t border-gray-100 text-sm text-gray-500">
                            Total pengajuan: <span class="font-bold text-gray-800">Rp {{ number_format($myTotal) }}</span>
                        </div>
                    @endif
                </div>

                
                <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-800">Perlu Approval Saya</h3>
                        @if($myPendingApprovals->count() > 0)
                            <a href="{{ route('approvals.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Lihat semua →</a>
                        @endif
                    </div>
                    @forelse($myPendingApprovals as $approval)
                        <a href="{{ route('approvals.show', $approval) }}"
                            class="flex items-center justify-between p-3 mb-2 rounded-xl border border-gray-100 hover:bg-indigo-50 hover:border-indigo-200 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700">{{ $approval->request->title ?? 'Request #'.$approval->request_id }}</p>
                                    <p class="text-xs text-gray-400">{{ $approval->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-gray-700">Rp {{ number_format($approval->request->total_amount) }}</span>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center text-gray-400">
                            <svg class="w-10 h-10 mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm">Tidak ada approval yang menunggu</p>
                        </div>
                    @endforelse
                </div>
            </div>

            
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Request Terbaru Saya</h3>
                    <a href="{{ route('requests.index') }}" class="text-xs text-indigo-600 hover:underline font-medium">Lihat semua →</a>
                </div>
                @if($recentRequests->isEmpty())
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        Belum ada request. <a href="{{ route('requests.create') }}" class="text-indigo-600 hover:underline">Buat sekarang →</a>
                    </div>
                @else
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Judul</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Client</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider text-xs">Status</th>
                                <th class="px-6 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider text-xs">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentRequests as $req)
                                @php
                                    $statusMap = [
                                        'submitted'          => ['label' => 'Submitted', 'class' => 'bg-blue-100 text-blue-700'],
                                        'pending'            => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-700'],
                                        'approved'           => ['label' => 'Approved', 'class' => 'bg-green-100 text-green-700'],
                                        'rejected'           => ['label' => 'Rejected', 'class' => 'bg-red-100 text-red-700'],
                                        'revision_requested' => ['label' => 'Revisi', 'class' => 'bg-purple-100 text-purple-700'],
                                    ];
                                    $badge = $statusMap[$req->status] ?? ['label' => $req->status, 'class' => 'bg-gray-100 text-gray-600'];
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('requests.show', $req) }}" class="font-medium text-gray-900 hover:text-indigo-700">
                                            {{ $req->title ?? 'Request #'.$req->id }}
                                        </a>
                                        <p class="text-xs text-gray-400">{{ $req->created_at->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">{{ $req->clientCode ? $req->clientCode->prefix.'-'.$req->clientCode->instansi_singkat : '—' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-semibold text-gray-800">Rp {{ number_format($req->total_amount) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
