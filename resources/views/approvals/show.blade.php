<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Review Request:') }} <span class="font-normal">{{ $approval->request->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900 grid grid-cols-1 md:grid-cols-2 gap-8">

                    <!-- Left: Request Details (Read-only subset of show) -->
                    <div>
                        <h3 class="text-lg font-bold mb-4 border-b pb-2">Request Details</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Requested By</span>
                                <span class="block text-sm text-gray-900">{{ $approval->request->user->name }}
                                    ({{ $approval->request->user->email }})</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Type & Client Code</span>
                                <span class="block text-sm text-gray-900 capitalize">{{ $approval->request->type }} ·
                                    {{ $approval->request->clientCode ? $approval->request->clientCode->prefix . '-' . $approval->request->clientCode->instansi_singkat : 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-500 uppercase">Total Amount</span>
                                <span class="block text-xl font-bold text-blue-700">Rp
                                    {{ number_format($approval->request->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="mt-4">
                                <span class="block text-xs font-bold text-gray-500 uppercase">Items Breakdown</span>
                                <ul class="mt-2 space-y-2">
                                    @foreach($approval->request->items as $item)
                                        <li class="bg-gray-50 p-2 rounded border text-sm">
                                            <div class="flex justify-between">
                                                <span class="font-bold">{{ str_replace('_', ' ', $item->type) }}</span>
                                                <span class="font-bold text-gray-800">Rp
                                                    {{ number_format($item->amount, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="text-gray-600 mt-1">{{ $item->description }}</div>
                                            @if($item->attachments->count() > 0)
                                                <div class="mt-1">
                                                    @foreach($item->attachments as $att)
                                                        <a href="{{ Storage::url($att->file_path) }}" target="_blank"
                                                            class="text-xs text-indigo-600 hover:text-indigo-800 underline flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13">
                                                                </path>
                                                            </svg> Attachment
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Action Form -->
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4 text-blue-900 border-b border-blue-200 pb-2">Your Approval
                            Action</h3>

                        @auth
                            @php
                                $step = $approval->step;
                                $authUser = Auth::user();

                                if ($step->isDivisionLevel()) {
                                    // Division-level: user harus dari divisi yang sama + level <= required_level
                                    $requesterDivId = $approval->request->user->division_id;
                                    $canAct = $authUser->division_id === $requesterDivId
                                        && $authUser->level !== null
                                        && $authUser->level <= $step->required_level;
                                } elseif ($step->isRoleLevel()) {
                                    $canAct = $step->role !== null
                                        && $authUser->hasRole($step->role->slug)
                                        && $authUser->level !== null
                                        && $authUser->level <= $step->required_level;
                                } else {
                                    $canAct = $step->role !== null && $authUser->hasRole($step->role->slug);
                                }

                                $stepLabel = $step->isDivisionLevel()
                                    ? 'Level ≤ ' . $step->required_level . ' (Divisi)'
                                    : ($step->role?->name ?? '—');
                            @endphp

                            @if($canAct)
                                <form action="{{ route('approvals.update', $approval->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Step Designation</label>
                                        <div class="p-2 bg-white rounded border text-sm font-mono text-gray-800">
                                            {{ $stepLabel }}
                                            (Step {{ $step->step_order }})
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Decision</label>
                                        <div class="space-y-2">
                                            <label
                                                class="flex items-center p-3 bg-white border rounded cursor-pointer hover:bg-green-50 ui-radio">
                                                <input type="radio" name="status" value="approved"
                                                    class="text-green-600 focus:ring-green-500 rounded-full" required>
                                                <span class="ml-2 font-bold text-green-700">Approve</span>
                                            </label>
                                            <label
                                                class="flex items-center p-3 bg-white border rounded cursor-pointer hover:bg-yellow-50 ui-radio">
                                                <input type="radio" name="status" value="revision"
                                                    class="text-yellow-600 focus:ring-yellow-500 rounded-full">
                                                <span class="ml-2 font-bold text-yellow-700">Request Revision</span>
                                            </label>
                                            <label
                                                class="flex items-center p-3 bg-white border rounded cursor-pointer hover:bg-red-50 ui-radio">
                                                <input type="radio" name="status" value="rejected"
                                                    class="text-red-600 focus:ring-red-500 rounded-full">
                                                <span class="ml-2 font-bold text-red-700">Reject Completely</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <label class="block text-sm font-bold text-gray-700 mb-2">Comments / Reasons</label>
                                        <textarea name="comments" rows="3"
                                            class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:border-blue-300"
                                            placeholder="Required if rejecting or revising..."></textarea>
                                    </div>

                                    <div class="flex justify-end gap-3 pt-4 border-t border-blue-200">
                                        <a href="{{ route('approvals.index') }}"
                                            class="px-4 py-2 bg-white border rounded text-gray-700 font-bold hover:bg-gray-50">Cancel</a>
                                        <button type="submit"
                                            class="px-6 py-2 bg-blue-600 rounded text-white font-bold hover:bg-blue-700 shadow">Submit
                                            Decision</button>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <p class="text-sm font-bold text-gray-500">Viewing Only</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        This approval step requires the <span
                                            class="font-semibold text-yellow-700 bg-yellow-100 px-1 rounded">{{ $approval->step->role->name }}</span>
                                        role.
                                    </p>
                                </div>
                            @endif
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>