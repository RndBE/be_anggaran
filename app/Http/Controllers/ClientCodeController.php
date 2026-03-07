<?php

namespace App\Http\Controllers;

use App\Models\ClientCode;
use Illuminate\Http\Request;
use App\Services\AuditLogService;

class ClientCodeController extends Controller
{
    public function index()
    {
        $clientCodes = ClientCode::orderBy('prefix')->get();
        return view('settings.client_codes.index', compact('clientCodes'));
    }

    public function create()
    {
        return view('settings.client_codes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string|max:20|unique:client_codes,prefix',
            'instansi_singkat' => 'required|string|max:50',
            'counter' => 'required|integer|min:0',
            'name' => 'nullable|string|max:255',
        ]);

        $cc = ClientCode::create($request->only('prefix', 'instansi_singkat', 'counter', 'name'));
        AuditLogService::log('client_code.created', $cc, $request->only('prefix', 'instansi_singkat', 'counter', 'name'));

        return redirect()->route('settings.client-codes.index')
            ->with('success', "Client Code \"{$request->prefix}\" berhasil ditambahkan.");
    }

    public function edit(ClientCode $clientCode)
    {
        return view('settings.client_codes.edit', compact('clientCode'));
    }

    public function update(Request $request, ClientCode $clientCode)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:20|unique:client_codes,prefix,' . $clientCode->id,
            'instansi_singkat' => 'required|string|max:50',
            'counter' => 'required|integer|min:0',
            'name' => 'nullable|string|max:255',
        ]);

        // Flash edit id so the modal re-opens on validation failure
        $request->merge(['_edit_id' => $clientCode->id]);
        $request->flash();

        AuditLogService::logChanges('client_code.updated', $clientCode, $clientCode->getOriginal(), $validated);
        $clientCode->update($validated);

        return redirect()->route('settings.client-codes.index')
            ->with('success', "Client Code \"{$clientCode->prefix}\" berhasil diperbarui.");
    }


    public function destroy(ClientCode $clientCode)
    {
        $prefix = $clientCode->prefix;
        AuditLogService::log('client_code.deleted', $clientCode, ['prefix' => $prefix]);
        $clientCode->delete();

        return redirect()->route('settings.client-codes.index')
            ->with('success', "Client Code \"{$prefix}\" berhasil dihapus.");
    }
}
