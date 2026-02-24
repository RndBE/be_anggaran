<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        $policies = Policy::orderBy('key')->get();
        return view('settings.policy.index', compact('policies'));
    }

    public function create()
    {
        return view('settings.policy.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'key' => 'required|string|max:100|unique:policies,key|regex:/^[A-Z0-9_]+$/',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        Policy::create($request->only('name', 'key', 'value', 'description'));

        return redirect()->route('settings.policies.index')
            ->with('success', "Policy \"{$request->name}\" berhasil dibuat.");
    }

    public function edit(Policy $policy)
    {
        return view('settings.policy.edit', compact('policy'));
    }

    public function update(Request $request, Policy $policy)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'key' => 'required|string|max:100|unique:policies,key,' . $policy->id . '|regex:/^[A-Z0-9_]+$/',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
        ]);

        $policy->update($request->only('name', 'key', 'value', 'description'));

        return redirect()->route('settings.policies.index')
            ->with('success', "Policy \"{$policy->name}\" berhasil diperbarui.");
    }

    public function destroy(Policy $policy)
    {
        $name = $policy->name;
        $policy->delete();

        return redirect()->route('settings.policies.index')
            ->with('success', "Policy \"{$name}\" dihapus.");
    }
}
