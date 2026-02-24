<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('settings.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('settings.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'slug' => 'required|string|max:100|unique:roles,slug|regex:/^[a-z0-9_-]+$/',
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$request->name}\" berhasil dibuat.");
    }

    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:100|unique:roles,slug,' . $role->id . '|regex:/^[a-z0-9_-]+$/',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$role->name}\" berhasil diperbarui.");
    }

    public function destroy(Role $role)
    {
        // Prevent deleting a role that still has active users
        if ($role->users()->exists()) {
            return back()->withErrors([
                'error' => "Tidak bisa menghapus role \"{$role->name}\" — masih ada user yang menggunakan role ini.",
            ]);
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('settings.roles.index')
            ->with('success', "Role \"{$roleName}\" berhasil dihapus.");
    }
}
