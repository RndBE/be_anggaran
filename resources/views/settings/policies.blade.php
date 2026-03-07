<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-foreground">Policies & Zones</h2>
            <p class="text-sm text-muted-foreground mt-0.5">Ringkasan kebijakan limit dan zona perjalanan dinas</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Reimbursement Limit Policies --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Reimbursement Limit Policies</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Policy Name</th>
                                <th>Key Identifier</th>
                                <th class="text-right">Current Value (Limit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($policies as $policy)
                                <tr>
                                    <td class="font-medium text-foreground">{{ $policy->name }}</td>
                                    <td><code class="text-xs font-mono text-muted-foreground">{{ $policy->key }}</code></td>
                                    <td class="text-right font-bold text-primary">Rp
                                        {{ number_format($policy->value, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Travel Zones --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-border">
                    <h3 class="card-title">Travel Zones & Meal Allowances</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Zone Level</th>
                                <th>Region Name</th>
                                <th class="text-right">Lumpsum Allowance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                                <tr>
                                    <td>
                                        <span
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary/10 text-primary font-bold text-xs">{{ $zone->zone }}</span>
                                    </td>
                                    <td class="font-medium text-foreground">{{ $zone->name }}</td>
                                    <td class="text-right font-bold text-primary">Rp
                                        {{ number_format($zone->meal_allowance, 0, ',', '.') }} / Hari</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>