<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-foreground">Audit Log</h2>
                <p class="text-sm text-muted-foreground mt-0.5">Riwayat semua aktivitas dalam sistem</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('settings.audit-logs.index') }}" class="card p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari action, model, IP…"
                            class="input w-full">
                    </div>
                    <div>
                        <select name="user_id" class="select-input w-full">
                            <option value="">Semua User</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="action" class="select-input w-full">
                            <option value="">Semua Action</option>
                            @foreach($actionGroups as $prefix => $label)
                                <option value="{{ $prefix }}" {{ request('action') === $prefix ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <div class="date-input-wrapper flex-1">
                            <input type="text" name="date_from" data-datepicker value="{{ request('date_from') }}"
                                class="flatpickr-input w-full text-sm" placeholder="Dari tanggal" readonly>
                            <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="date-input-wrapper flex-1">
                            <input type="text" name="date_to" data-datepicker value="{{ request('date_to') }}"
                                class="flatpickr-input w-full text-sm" placeholder="Sampai tanggal" readonly>
                            <svg class="date-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <button type="submit" class="btn-default btn-sm">Filter</button>
                    <a href="{{ route('settings.audit-logs.index') }}" class="btn-outline btn-sm">Reset</a>
                    <span class="text-xs text-muted-foreground ml-auto">{{ number_format($logs->total()) }} log
                        ditemukan</span>
                </div>
            </form>

            {{-- Table --}}
            <div class="card overflow-hidden">
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th class="w-36">Waktu</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Model</th>
                                <th>Changes</th>
                                <th class="w-28">IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @php
                                    $actionBadge = match (true) {
                                        str_contains($log->action, 'created') => 'badge-success',
                                        str_contains($log->action, 'updated') => 'badge-info',
                                        str_contains($log->action, 'deleted') => 'badge-destructive',
                                        str_contains($log->action, 'approved') => 'badge-success',
                                        str_contains($log->action, 'rejected') => 'badge-warning',
                                        str_contains($log->action, 'login') => 'badge-secondary',
                                        str_contains($log->action, 'logout') => 'badge-secondary',
                                        default => 'badge-purple',
                                    };
                                @endphp
                                <tr class="align-top">
                                    <td class="text-xs text-muted-foreground whitespace-nowrap">
                                        {{ $log->created_at->format('d/m/y H:i:s') }}
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="font-medium text-foreground text-sm">{{ $log->user->name }}</span>
                                        @else
                                            <span class="text-muted-foreground italic text-sm">System</span>
                                        @endif
                                    </td>
                                    <td><span class="{{ $actionBadge }} text-xs">{{ $log->action }}</span></td>
                                    <td class="text-xs text-muted-foreground">
                                        @if($log->model_type)
                                            <span class="font-mono">{{ $log->model_type }}</span>
                                            @if($log->model_id)<span
                                            class="text-muted-foreground/60">#{{ $log->model_id }}</span>@endif
                                        @else
                                            <span class="text-muted-foreground/40">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-muted-foreground max-w-xs">
                                        @if($log->changes)
                                            <details>
                                                <summary class="cursor-pointer text-primary hover:underline font-medium">
                                                    {{ count($log->changes) }} perubahan
                                                </summary>
                                                <div class="mt-1 space-y-0.5 pl-2">
                                                    @foreach($log->changes as $field => $val)
                                                        <div class="font-mono text-xs">
                                                            <span class="text-muted-foreground">{{ $field }}:</span>
                                                            @if(is_array($val) && isset($val['from']))
                                                                <span
                                                                    class="line-through text-destructive">{{ Str::limit((string) $val['from'], 40) }}</span>
                                                                → <span
                                                                    class="text-green-600">{{ Str::limit((string) $val['to'], 40) }}</span>
                                                            @else
                                                                <span>{{ Str::limit(is_array($val) ? json_encode($val) : (string) $val, 60) }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </details>
                                        @else
                                            <span class="text-muted-foreground/40">—</span>
                                        @endif
                                    </td>
                                    <td class="text-xs text-muted-foreground font-mono">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-sm text-muted-foreground">Belum ada audit
                                        log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $logs->links() }}</div>

        </div>
    </div>
</x-app-layout>