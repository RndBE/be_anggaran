<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">{{ __('Approval Inbox') }}</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Pengajuan yang membutuhkan persetujuan Anda</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert-success mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Pending Your Approval</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Date Submitted</th>
                                <th>Requested By</th>
                                <th>Title / Type</th>
                                <th>Amount</th>
                                <th>Required Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvals as $approval)
                                <tr>
                                    <td class="text-sm text-muted-foreground whitespace-nowrap">
                                        {{ $approval->request->created_at->format('d M Y') }}
                                    </td>
                                    <td class="font-medium text-foreground">{{ $approval->request->user->name }}</td>
                                    <td>
                                        <div class="font-semibold text-foreground">{{ $approval->request->title }}</div>
                                        <span class="badge-secondary mt-1">{{ strtoupper($approval->request->type) }}</span>
                                    </td>
                                    <td class="font-bold text-primary whitespace-nowrap">
                                        Rp {{ number_format($approval->request->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <span class="badge-warning">
                                            @if($approval->step->isDivisionLevel())
                                                Level ≤ {{ $approval->step->required_level }} (Divisi)
                                            @else
                                                {{ $approval->step->role?->name ?? '—' }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('approvals.show', $approval->id) }}" class="btn-default btn-sm">
                                            Review →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-14 text-center">
                                        <div
                                            class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-medium text-foreground">All caught up!</p>
                                        <p class="text-xs text-muted-foreground mt-1">No pending approvals requiring your
                                            attention.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>