<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'division')->get();
        return view('settings.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $divisions = Division::all();
        return view('settings.users.create', compact('roles', 'divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'division_id' => 'nullable|exists:divisions,id',
            'level' => 'nullable|integer|min:1|max:4',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ]);

        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'division_id' => $request->division_id,
            'level' => $request->level,
            'signature' => $signaturePath,
        ]);

        if ($request->roles) {
            $user->roles()->attach($request->roles);
        }

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$user->name}\" berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $user->load('roles', 'division');
        $roles = Role::all();
        $divisions = Division::all();
        return view('settings.users.edit', compact('user', 'roles', 'divisions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Rules\Password::defaults()],
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'division_id' => 'nullable|exists:divisions,id',
            'level' => 'nullable|integer|min:1|max:4',
            'signature' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'division_id' => $request->division_id,
            'level' => $request->level,
        ];

        // Handle signature upload
        if ($request->hasFile('signature')) {
            // Delete old signature if exists
            if ($user->signature) {
                \Storage::disk('public')->delete($user->signature);
            }
            $updateData['signature'] = $request->file('signature')->store('signatures', 'public');
        }

        $user->update($updateData);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->roles()->sync($request->roles ?? []);

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak bisa menghapus akun Anda sendiri.']);
        }

        $pendingRequests = $user->requests()->whereIn('status', ['submitted', 'pending'])->count();
        if ($pendingRequests > 0) {
            return back()->withErrors(['error' => "User \"{$user->name}\" masih punya {$pendingRequests} request yang belum selesai."]);
        }

        $userName = $user->name;
        $user->roles()->detach();
        $user->delete();

        return redirect()->route('settings.users.index')
            ->with('success', "User \"{$userName}\" berhasil dihapus.");
    }
}
