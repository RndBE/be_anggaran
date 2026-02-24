<?php

namespace App\Http\Controllers;

use App\Models\TravelZone;
use Illuminate\Http\Request;

class TravelZoneController extends Controller
{
    public function index()
    {
        $zones = TravelZone::orderBy('zone')->get();
        return view('settings.travel_zones.index', compact('zones'));
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

        TravelZone::create($request->only('zone', 'name', 'meal_allowance'));

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$request->zone} berhasil dibuat.");
    }

    public function edit(TravelZone $travelZone)
    {
        return view('settings.travel_zones.edit', compact('travelZone'));
    }

    public function update(Request $request, TravelZone $travelZone)
    {
        $request->validate([
            'zone' => 'required|integer|min:1|unique:travel_zones,zone,' . $travelZone->id,
            'name' => 'required|string|max:100',
            'meal_allowance' => 'required|numeric|min:0',
        ]);

        $travelZone->update($request->only('zone', 'name', 'meal_allowance'));

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$travelZone->zone} berhasil diperbarui.");
    }

    public function destroy(TravelZone $travelZone)
    {
        $zone = $travelZone->zone;
        $travelZone->delete();

        return redirect()->route('settings.travel-zones.index')
            ->with('success', "Zone {$zone} berhasil dihapus.");
    }
}
