<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-foreground">Dashboard</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Selamat datang, <span class="font-medium text-primary">{{ Auth::user()->name }}</span>
                    @if(Auth::user()->division)
                        — <span class="text-foreground">{{ Auth::user()->division->name }}</span>
                        @if(Auth::user()->level)
                            <span class="ms-1 badge badge-info">Level {{ Auth::user()->level }}</span>
                        @endif
                    @endif
                </p>
            </div>
            <p class="text-sm text-muted-foreground">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($canManage)
            {{-- Stat Cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @php
                $statCards = [
                    ['label' => 'Total Requests', 'value' => number_format($systemStats['total_requests']), 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'color' => 'text-primary bg-primary/10', 'valueClass' => 'text-foreground'],
                    ['label' => 'Menunggu Approval', 'value' => number_format($systemStats['pending']), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-amber-600 bg-amber-50', 'valueClass' => 'text-amber-600'],
                    ['label' => 'Disetujui', 'value' => number_format($systemStats['approved']), 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-green-600 bg-green-50', 'valueClass' => 'text-green-600'],
                    ['label' => 'Total Disetujui', 'value' => 'Rp '.number_format($systemStats['approved_amount']/1000000, 1).'Jt', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-blue-600 bg-blue-50', 'valueClass' => 'text-blue-700'],
                ];
                @endphp
                @foreach($statCards as $card)
                    <div class="card p-5 flex items-center gap-4">
                        <div class="w-11 h-11 rounded-xl {{ $card['color'] }} flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold {{ $card['valueClass'] }}">{{ $card['value'] }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $card['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pipeline + Spending --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 card p-6">
                    <h3 class="card-title mb-4">Pipeline Requests</h3>
                    @php
                        $total = max($systemStats['total_requests'], 1);
                        $pipeline = [
                            ['label' => 'Pending', 'count' => $systemStats['pending'], 'color' => 'bg-amber-400', 'text' => 'text-amber-700'],
                            ['label' => 'Disetujui', 'count' => $systemStats['approved'], 'color' => 'bg-green-400', 'text' => 'text-green-700'],
                            ['label' => 'Ditolak', 'count' => $systemStats['rejected'], 'color' => 'bg-red-400', 'text' => 'text-red-700'],
                            ['label' => 'Revisi', 'count' => $systemStats['revision'], 'color' => 'bg-purple-400', 'text' => 'text-purple-700'],
                        ];
                    @endphp
                    <div class="flex h-3 rounded-full overflow-hidden gap-0.5 mb-4">
                        @foreach($pipeline as $p)
                            @php $pct = round($p['count'] / $total * 100); @endphp
                            @if($p['count'] > 0)
                                <div class="{{ $p['color'] }} rounded-full" style="width: {{ $pct }}%" title="{{ $p['label'] }}: {{ $p['count'] }}"></div>
                            @endif
                        @endforeach
                    </div>
                    <div class="flex flex-wrap gap-4">
                        @foreach($pipeline as $p)
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $p['color'] }}"></span>
                                <span class="text-sm text-muted-foreground">{{ $p['label'] }}</span>
                                <span class="text-sm font-bold {{ $p['text'] }}">{{ $p['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card p-6">
                    <h3 class="card-title mb-4">Pengeluaran / Kategori</h3>
                    @php $maxSpend = $spendingByType->max('total') ?: 1; @endphp
                    <div class="space-y-3">
                        @forelse($spendingByType as $item)
                            <div>
                                <div class="flex justify-between text-xs text-muted-foreground mb-1">
                                    <span class="capitalize font-medium text-foreground">{{ str_replace('_', ' ', $item->type) }}</span>
                                    <span class="font-semibold">Rp {{ number_format($item->total/1000, 0) }}rb</span>
                                </div>
                                <div class="h-1.5 bg-muted rounded-full overflow-hidden">
                                    <div class="h-1.5 rounded-full bg-primary transition-all" style="width: {{ round($item->total / $maxSpend * 100) }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-muted-foreground italic">Belum ada data.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Monthly Trend --}}
            <div class="card p-6">
                <h3 class="card-title mb-5">Tren Request — 6 Bulan Terakhir</h3>
                @php $maxCount = max($months->max('count'), 1); @endphp
                <div class="flex items-end gap-2 h-28">
                    @foreach($months as $key => $m)
                        @php
                            $barH = max(round(($m->count / $maxCount) * 100), 4);
                            $label = \Carbon\Carbon::createFromFormat('Y-m', $key)->locale('id')->isoFormat('MMM');
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 group cursor-default">
                            <span class="text-xs text-muted-foreground font-semibold opacity-0 group-hover:opacity-100 transition-opacity">{{ $m->count }}</span>
                            <div class="w-full rounded-t-md bg-primary/20 hover:bg-primary/40 transition-colors"
                                style="height: {{ $barH }}%"
                                title="{{ $label }}: {{ $m->count }} request (Rp {{ number_format($m->total/1000) }}rb)">
                            </div>
                            <span class="text-xs text-muted-foreground capitalize">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- My Request Stats + Pending Approvals --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title">Request Saya</h3>
                        <span class="text-2xl font-bold text-primary">{{ $myCount }}</span>
                    </div>
                    @php
                        $statusConfig = [
                            'submitted'          => ['label' => 'Submitted', 'badge' => 'badge-info'],
                            'pending'            => ['label' => 'Pending', 'badge' => 'badge-warning'],
                            'approved'           => ['label' => 'Disetujui', 'badge' => 'badge-success'],
                            'rejected'           => ['label' => 'Ditolak', 'badge' => 'badge-destructive'],
                            'revision_requested' => ['label' => 'Revisi', 'badge' => 'badge-purple'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($statusConfig as $status => $cfg)
                            @php $count = $myRequests[$status] ?? 0; @endphp
                            @if($count > 0)
                                <div class="flex items-center justify-between">
                                    <span class="{{ $cfg['badge'] }}">{{ $cfg['label'] }}</span>
                                    <span class="font-bold text-foreground">{{ $count }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @if($myTotal > 0)
                        <div class="mt-4 pt-3 border-t border-border text-sm text-muted-foreground">
                            Total: <span class="font-bold text-foreground">Rp {{ number_format($myTotal) }}</span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-2 card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="card-title">Perlu Approval Saya</h3>
                        @if($myPendingApprovals->count() > 0)
                            <a href="{{ route('approvals.index') }}" class="text-xs text-primary hover:underline font-medium">Lihat semua →</a>
                        @endif
                    </div>
                    @forelse($myPendingApprovals as $approval)
                        <a href="{{ route('approvals.show', $approval) }}"
                            class="flex items-center justify-between p-3 mb-2 rounded-lg border border-border hover:bg-accent hover:border-primary/20 transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-foreground group-hover:text-primary">{{ $approval->request->title ?? 'Request #'.$approval->request_id }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $approval->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-foreground">Rp {{ number_format($approval->request->total_amount) }}</span>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-foreground">Semua sudah diproses!</p>
                            <p class="text-xs text-muted-foreground mt-1">Tidak ada approval yang menunggu</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent Requests Table --}}
            <div class="card overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                    <h3 class="card-title">Request Terbaru Saya</h3>
                    <a href="{{ route('requests.index') }}" class="text-xs text-primary hover:underline font-medium">Lihat semua →</a>
                </div>
                @if($recentRequests->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-muted-foreground">Belum ada request. <a href="{{ route('requests.create') }}" class="text-primary hover:underline">Buat sekarang →</a></p>
                    </div>
                @else
                    <div class="table-wrapper">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th class="text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRequests as $req)
                                    @php
                                        $badgeMap = [
                                            'submitted'          => ['label' => 'Submitted', 'class' => 'badge-info'],
                                            'pending'            => ['label' => 'Pending', 'class' => 'badge-warning'],
                                            'approved'           => ['label' => 'Approved', 'class' => 'badge-success'],
                                            'rejected'           => ['label' => 'Rejected', 'class' => 'badge-destructive'],
                                            'revision_requested' => ['label' => 'Revisi', 'class' => 'badge-purple'],
                                        ];
                                        $badge = $badgeMap[$req->status] ?? ['label' => $req->status, 'class' => 'badge-secondary'];
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('requests.show', $req) }}" class="font-medium text-foreground hover:text-primary">
                                                {{ $req->title ?? 'Request #'.$req->id }}
                                            </a>
                                            <p class="text-xs text-muted-foreground">{{ $req->created_at->format('d M Y') }}</p>
                                        </td>
                                        <td class="text-muted-foreground text-sm">{{ $req->clientCode ? $req->clientCode->prefix.'-'.$req->clientCode->instansi_singkat : '—' }}</td>
                                        <td><span class="{{ $badge['class'] }}">{{ $badge['label'] }}</span></td>
                                        <td class="text-right font-semibold text-foreground">Rp {{ number_format($req->total_amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
