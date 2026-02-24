<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->get();
        return view('settings.divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('settings.divisions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name',
            'slug' => 'required|string|max:100|unique:divisions,slug|regex:/^[a-z0-9_-]+$/',
        ]);

        Division::create($request->only('name', 'slug'));

        return redirect()->route('settings.divisions.index')
            ->with('success', "Divisi \"{$request->name}\" berhasil dibuat.");
    }

    public function edit(Division $division)
    {
        return view('settings.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name,' . $division->id,
            'slug' => 'required|string|max:100|unique:divisions,slug,' . $division->id . '|regex:/^[a-z0-9_-]+$/',
        ]);

        $division->update($request->only('name', 'slug'));

        return redirect()->route('settings.divisions.index')
            ->with('success', "Divisi \"{$division->name}\" berhasil diperbarui.");
    }

    public function destroy(Division $division)
    {
        if ($division->users()->exists()) {
            return back()->withErrors(['error' => "Tidak bisa menghapus divisi \"{$division->name}\" — masih ada karyawan di divisi ini."]);
        }

        $name = $division->name;
        $division->delete();

        return redirect()->route('settings.divisions.index')
            ->with('success', "Divisi \"{$name}\" dihapus.");
    }
}
