<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Flow Builder') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-3">

            <!-- Create New Flow Form -->
            <div class="p-4 sm:px-8 py-4 bg-white shadow sm:rounded-lg" x-data="flowBuilder()">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Create New Workflow') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Design a custom approval routing sequence.') }}
                    </p>
                </header>

                <form method="POST" action="{{ route('settings.flows.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Flow Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus
                            placeholder="e.g., Marketing Campaign Flow" />
                    </div>

                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full"
                            placeholder="Optional brief explanation" />
                    </div>

                    <div class="mt-6">
                        <h3 class="text-md font-medium text-gray-900 mb-2">Approval Steps Sequence</h3>
                        <p class="text-sm text-gray-500 mb-4">Define who needs to approve requests using this flow, in
                            order from first to last.</p>

                        <div class="space-y-3">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex items-center space-x-4 bg-gray-50 p-3 rounded border">
                                    <div class="font-bold text-gray-500 px-3 py-1 bg-white border rounded">
                                        <span x-text="index + 1"></span>
                                    </div>

                                    <div class="flex-1">
                                        <select x-model="step.role_id" :name="`steps[${index}][role_id]`"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
                                            required>
                                            <option value="" disabled>Select Role...</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex items-center mt-1">
                                        <input type="checkbox" :name="`steps[${index}][requires_director]`" value="1"
                                            x-model="step.requires_director"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <label class="ml-2 text-sm text-gray-600">Requires Director Exception?</label>
                                    </div>

                                    <button type="button" @click="removeStep(index)"
                                        class="text-red-500 hover:text-red-700 ml-4 font-bold"
                                        x-show="steps.length > 1">
                                        &times; Remove
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="mt-4">
                            <button type="button" @click="addStep"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm font-semibold inline-flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Step
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6 pt-4 border-t">
                        <x-primary-button>{{ __('Save Flow') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Existing Flows List -->
            <div class="p-4 sm:px-8 py-4 bg-white shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Existing Approval Flows') }}
                    </h2>
                </header>

                @if(session('success'))
                    <div class="mt-4 p-3 bg-green-100 border border-green-200 text-green-700 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->has('error'))
                    <div class="mt-4 p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-sm">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <div class="mt-3 space-y-3">
                    @forelse($flows as $flow)
                        <div class="border rounded-lg p-4 hover:border-indigo-200 transition-colors">
                            {{-- Header row --}}
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-md font-bold text-gray-900">{{ $flow->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $flow->description ?? 'No description' }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    {{-- Edit Button --}}
                                    <a href="{{ route('settings.flows.edit', $flow) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    {{-- Delete Button --}}
                                    <form method="POST" action="{{ route('settings.flows.destroy', $flow) }}"
                                        onsubmit="return confirm('Hapus flow &laquo;{{ $flow->name }}&raquo;? Semua approval step di dalamnya akan dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Steps flow diagram --}}
                            <div class="flex items-center flex-wrap gap-1 text-sm">
                                <span class="font-semibold text-gray-500 text-xs uppercase tracking-wider">Start</span>
                                @foreach($flow->steps as $step)
                                                    <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-semibold
                                                                                                                                                                                                                                        {{ $step->requires_director
                                    ? 'bg-red-50 border border-dashed border-red-300 text-red-700'
                                    : 'bg-indigo-50 border border-indigo-200 text-indigo-700' }}">
                                                        <span class="text-gray-400 mr-0.5">{{ $step->step_order }}.</span>
                                                        @if($step->required_level)
                                                            Lv≤{{ $step->required_level }}
                                                            <span class="text-indigo-400 font-normal">(Div)</span>
                                                        @elseif($step->role)
                                                            {{ $step->role->name }}
                                                        @else
                                                            —
                                                        @endif
                                                        @if($step->requires_director)
                                                            <span class="text-red-400 font-normal">(Dir)</span>
                                                        @endif
                                                    </div>
                                @endforeach
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <span
                                    class="font-semibold text-green-600 border-2 border-green-500 px-2 py-0.5 rounded text-xs">Done</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4">No approval flows created yet.</p>
                    @endforelse
                </div>
            </div>


        </div>
    </div>

    <!-- Alpine.js logic for Flow Builder -->
    <script>
        function flowBuilder() {
            return {
                steps: [
                    { role_id: '', requires_director: false }
                ],
                addStep() {
                    this.steps.push({ role_id: '', requires_director: false });
                },
                removeStep(index) {
                    if (this.steps.length > 1) {
                        this.steps.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-app-layout>