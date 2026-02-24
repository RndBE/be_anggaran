<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Zone: <span class="font-normal text-indigo-600">Zone {{ $travelZone->zone }} —
                    {{ $travelZone->name }}</span>
            </h2>
            <a href="{{ route('settings.travel-zones.index') }}" class="text-sm text-gray-500 hover:text-gray-700">←
                Kembali</a>
        </div>
    </x-slot>

    <div class="py-5">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg p-6 sm:px-8 py-4">
                <form method="POST" action="{{ route('settings.travel-zones.update', $travelZone) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="zone" :value="__('Nomor Zone')" />
                            <x-text-input id="zone" name="zone" type="number" min="1" class="mt-1 block w-full"
                                value="{{ old('zone', $travelZone->zone) }}" required autofocus />
                        </div>
                        <div>
                            <x-input-label for="name" :value="__('Nama Zone')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                value="{{ old('name', $travelZone->name) }}" required />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="meal_allowance" :value="__('Meal Allowance / Hari (Rp)')" />
                        <div class="mt-1 relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                            <x-text-input id="meal_allowance" name="meal_allowance" type="number" step="1000" min="0"
                                class="block w-full pl-10"
                                value="{{ old('meal_allowance', $travelZone->meal_allowance) }}" required />
                        </div>
                    </div>
                    <div class="pt-3 border-t flex justify-between">
                        <a href="{{ route('settings.travel-zones.index') }}"
                            class="px-4 py-2 text-sm text-gray-600 bg-white border rounded-lg hover:bg-gray-50">Batal</a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>