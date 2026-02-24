<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Flow: <span class="font-normal text-indigo-600">{{ $flow->name }}</span>
            </h2>
            <a href="{{ route('settings.flows') }}" class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Flows
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 sm:p-8"
                x-data="flowEditor({{ json_encode($flow->steps->map(fn($s) => ['role_id' => (string) $s->role_id, 'requires_director' => (bool) $s->requires_director])) }})">

                <form method="POST" action="{{ route('settings.flows.update', $flow) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Flow Name --}}
                    <div>
                        <x-input-label for="name" :value="__('Flow Name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                            value="{{ old('name', $flow->name) }}" required />
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-input-label for="description" :value="__('Description')" />
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full"
                            value="{{ old('description', $flow->description) }}" placeholder="Optional" />
                    </div>

                    {{-- Steps Sequence --}}
                    <div>
                        <h3 class="text-md font-medium text-gray-900 mb-1">Approval Steps Sequence</h3>
                        <p class="text-sm text-gray-500 mb-4">Drag to reorder (edit order by remove & re-add). First
                            step is triggered first.</p>

                        <div class="space-y-3">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                    {{-- Step number badge --}}
                                    <div
                                        class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm">
                                        <span x-text="index + 1"></span>
                                    </div>

                                    {{-- Role selector --}}
                                    <div class="flex-1">
                                        <select x-model="step.role_id" :name="`steps[${index}][role_id]`"
                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm"
                                            required>
                                            <option value="" disabled>Select Role...</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Director flag --}}
                                    <label
                                        class="flex items-center gap-2 text-sm text-gray-600 shrink-0 cursor-pointer">
                                        <input type="checkbox" :name="`steps[${index}][requires_director]`" value="1"
                                            x-model="step.requires_director"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        Director?
                                    </label>

                                    {{-- Remove --}}
                                    <button type="button" @click="removeStep(index)" x-show="steps.length > 1"
                                        class="shrink-0 text-red-400 hover:text-red-600 transition-colors"
                                        title="Remove step">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Add Step --}}
                        <button type="button" @click="addStep"
                            class="mt-3 inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Step
                        </button>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <a href="{{ route('settings.flows') }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 shadow-sm transition-colors">
                            Update Flow
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        function flowEditor(initialSteps) {
            return {
                steps: initialSteps.length ? initialSteps : [{ role_id: '', requires_director: false }],
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