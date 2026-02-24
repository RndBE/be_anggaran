<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Policy;
use App\Models\TravelZone;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Division;

class SettingsController extends Controller
{
    public function policies()
    {
        $policies = Policy::all();
        $zones = TravelZone::all();
        return view('settings.policies', compact('policies', 'zones'));
    }

    public function flows()
    {
        $flows = \App\Models\ApprovalFlow::with('steps.role', 'division')->get();
        $roles = \App\Models\Role::all();
        $divisions = Division::all();
        return view('settings.flows', compact('flows', 'roles', 'divisions'));
    }

    public function storeFlow(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'division_id' => 'nullable|exists:divisions,id',
            'steps' => 'required|array',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $flow = \App\Models\ApprovalFlow::create([
                'name' => $request->name,
                'description' => $request->description,
                'division_id' => $request->division_id,
                'is_active' => true,
            ]);

            foreach ($request->steps as $index => $step) {
                $flow->steps()->create([
                    'role_id' => !empty($step['role_id']) ? $step['role_id'] : null,
                    'required_level' => !empty($step['required_level']) ? (int) $step['required_level'] : null,
                    'step_order' => $index + 1,
                    'requires_director' => isset($step['requires_director']),
                ]);
            }
        });

        return redirect()->route('settings.flows')->with('success', 'New Approval Flow Created.');
    }

    public function editFlow(\App\Models\ApprovalFlow $flow)
    {
        $flow->load('steps.role');
        $roles = \App\Models\Role::all();
        $divisions = Division::all();
        return view('settings.flows_edit', compact('flow', 'roles', 'divisions'));
    }

    public function updateFlow(\Illuminate\Http\Request $request, \App\Models\ApprovalFlow $flow)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'division_id' => 'nullable|exists:divisions,id',
            'steps' => 'required|array',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $flow) {
            $flow->update([
                'name' => $request->name,
                'description' => $request->description,
                'division_id' => $request->division_id,
            ]);

            $flow->steps()->delete();

            foreach ($request->steps as $index => $step) {
                $flow->steps()->create([
                    'role_id' => !empty($step['role_id']) ? $step['role_id'] : null,
                    'required_level' => !empty($step['required_level']) ? (int) $step['required_level'] : null,
                    'step_order' => $index + 1,
                    'requires_director' => isset($step['requires_director']),
                ]);
            }
        });

        return redirect()->route('settings.flows')->with('success', 'Approval Flow updated successfully.');
    }

    public function destroyFlow(\App\Models\ApprovalFlow $flow)
    {
        // Check if any pending approvals rely on this flow's steps before deleting
        $inUse = $flow->steps()->whereHas('approvals', fn($q) => $q->where('status', 'pending'))->exists();

        if ($inUse) {
            return back()->withErrors(['error' => 'Cannot delete this flow — it has pending approvals in progress.']);
        }

        $flow->steps()->delete();
        $flow->delete();

        return redirect()->route('settings.flows')->with('success', 'Approval Flow deleted.');
    }

    public function permissions()
    {
        $permissions = Permission::with('roles')->get();
        $roles = Role::all();
        return view('settings.permissions', compact('permissions', 'roles'));
    }

    public function updatePermissions(Request $request)
    {
        $all = Permission::all();

        foreach ($all as $permission) {
            // Get array of checked role IDs for this permission (empty array if none checked)
            $roleIds = $request->input("perm_{$permission->id}", []);
            $permission->roles()->sync($roleIds);
        }

        return back()->with('success', 'Permission berhasil diperbarui.');
    }
}
