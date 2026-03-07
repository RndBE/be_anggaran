<?php

namespace App\Http\Controllers;

use App\Models\TravelZone;
use Illuminate\Http\Request;
use App\Services\AuditLogService;

class TravelZoneController extends Controller
{
    public function index()
    {
        $zones = TravelZone::orderBy('zone')->get();
        $nextZone = TravelZone::max('zone') + 1;
        return view('settings.travel_zones.index', compact('zones', 'nextZone'));
    }

    public function create()
    {
        $nextZone = TravelZone::max('zone') + 1;
        return view('settings.travel_zones.create', compact('nextZone'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'zone' => 'required|integer|min:1|unique:travel_zones,zone',
            'name' => 'required|string|max:100',
            'meal_allowance' => 'required|numeric|min:0',
        ]);

        $zone = TravelZone::create($request->only('zone', 'name', 'meal_allowance'));
        AuditLogService::log('travel_zone.created', $zone, $request->only('zone', 'name', 'meal_allowance'));

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$request->zone} berhasil dibuat.");
    }

    public function edit(TravelZone $travelZone)
    {
        return view('settings.travel_zones.edit', compact('travelZone'));
    }

    public function update(Request $request, TravelZone $travelZone)
    {
        $validated = $request->validate([
            'zone' => 'required|integer|min:1|unique:travel_zones,zone,' . $travelZone->id,
            'name' => 'required|string|max:100',
            'meal_allowance' => 'required|numeric|min:0',
        ]);

        // Flash edit id so the modal re-opens on validation failure
        $request->merge(['_edit_id' => $travelZone->id]);
        $request->flash();

        AuditLogService::logChanges('travel_zone.updated', $travelZone, $travelZone->getOriginal(), $validated);
        $travelZone->update($validated);

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$travelZone->zone} berhasil diperbarui.");
    }

    public function destroy(TravelZone $travelZone)
    {
        $zone = $travelZone->zone;
        AuditLogService::log('travel_zone.deleted', $travelZone, ['zone' => $zone]);
        $travelZone->delete();

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$zone} berhasil dihapus.");
    }
}
