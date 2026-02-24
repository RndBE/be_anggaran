<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Request Details:') }} <span class="font-normal">{{ $request->title }}</span>
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Left Col: Details & Items -->
                        <div class="md:col-span-2 space-y-6">

                            <!-- Header Info -->
                            <div class="bg-gray-50 border rounded-lg p-5">
                                <div class="flex justify-between border-b pb-3 mb-3">
                                    <div>
                                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Type</p>
                                        <p class="text-lg">{{ ucfirst($request->type) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">Status</p>
                                        @php
                                            $statusColor = match ($request->status) {
                                                'approved', 'paid' => 'bg-green-100 text-green-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                                'revision_requested' => 'bg-yellow-100 text-yellow-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Client Code
                                        </p>
                                        <p
                                            class="font-medium text-gray-900 border border-gray-300 p-2 mt-1 rounded bg-white inline-block">
                                            {{ $request->clientCode ? $request->clientCode->prefix . '-' . $request->clientCode->instansi_singkat : 'N/A' }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Amount
                                        </p>
                                        <p class="text-2xl font-bold text-blue-700 mt-1">
                                            Rp {{ number_format($request->total_amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Description</p>
                                    <p class="mt-1 text-gray-800">
                                        {{ $request->description ?: 'No description provided.' }}</p>
                                </div>
                            </div>

                            <!-- Items List -->
                            <div>
                                <h3 class="text-lg font-bold mb-3">Line Items</h3>
                                <div class="space-y-4">
                                    @foreach($request->items as $item)
                                        <div class="border rounded-lg overflow-hidden">
                                            <div
                                                class="bg-blue-50 p-3 flex justify-between items-center border-b border-blue-100">
                                                <div>
                                                    <span
                                                        class="uppercase tracking-wider text-xs font-bold text-blue-800 bg-white px-2 py-1 rounded border border-blue-200">
                                                        {{ str_replace('_', ' ', $item->type) }}
                                                    </span>
                                                    @if($item->type === 'entertain')
                                                        <span
                                                            class="ml-2 text-xs font-bold text-red-600 border border-red-200 bg-red-50 px-2 py-1 rounded">Butuh
                                                            Direktur</span>
                                                    @endif
                                                </div>
                                                <div class="font-bold text-gray-800">
                                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="p-3 bg-white">
                                                <p class="text-sm text-gray-700">{{ $item->description }}</p>

                                                @if($item->attachments->count() > 0)
                                                    <div class="mt-3 pt-3 border-t">
                                                        <p class="text-xs text-gray-500 font-bold mb-2 uppercase">Attachments
                                                        </p>
                                                        <div class="flex gap-2">
                                                            @foreach($item->attachments as $att)
                                                                <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                                    class="inline-flex items-center text-xs text-indigo-600 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 py-1 px-2 rounded">
                                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                                        viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                                        </path>
                                                                    </svg>
                                                                    View File
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Col: Timeline & Approvals -->
                        <div>
                            <div class="bg-gray-50 border rounded-lg p-5 sticky top-6">
                                <h3 class="text-lg font-bold mb-4 border-b pb-2">Approval Timeline</h3>

                                <div class="space-y-6">
                                    <!-- Submit node -->
                                    <div class="relative pl-6 border-l-2 border-indigo-200">
                                        <div
                                            class="absolute -left-[9px] top-1 h-4 w-4 rounded-full bg-indigo-500 ring-4 ring-white">
                                        </div>
                                        <p class="text-sm font-bold text-gray-900">Submitted</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $request->created_at->format('d M Y, H:i') }}</p>
                                        <p class="text-xs text-indigo-600 mt-1">By: {{ $request->user->name }}</p>
                                    </div>

                                    @foreach($request->approvals as $approval)
                                        @php
                                            $nodeColor = match ($approval->status) {
                                                'approved' => 'bg-green-500',
                                                'rejected' => 'bg-red-500',
                                                'revision' => 'bg-yellow-500',
                                                default => 'bg-gray-300'
                                            };
                                            $borderColor = match ($approval->status) {
                                                'approved' => 'border-green-200',
                                                'rejected' => 'border-red-200',
                                                'revision' => 'border-yellow-200',
                                                default => 'border-gray-200'
                                            };
                                        @endphp
                                        <div class="relative pl-6 border-l-2 {{ $borderColor }}">
                                            <div
                                                class="absolute -left-[9px] top-1 h-4 w-4 rounded-full {{ $nodeColor }} ring-4 ring-white">
                                            </div>
                                            <p class="text-sm font-bold text-gray-900">
                                                Review: {{ $approval->step->role->name ?? 'Approver' }}
                                            </p>
                                            <span
                                                class="text-[10px] font-bold uppercase tracking-wider {{ str_replace('bg-', 'text-', str_replace('500', '600', $nodeColor)) }}">
                                                {{ $approval->status }}
                                            </span>

                                            @if($approval->approver)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    {{ $approval->updated_at->format('d M Y, H:i') }}</p>
                                                <p class="text-xs text-gray-600">By: {{ $approval->approver->name }}</p>
                                            @endif

                                            @if($approval->comments)
                                                <div class="mt-2 bg-white border rounded p-2 text-xs italic text-gray-600">
                                                    "{{ $approval->comments }}"
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>