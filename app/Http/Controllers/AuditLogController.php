<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter keyword (action atau model)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('action', 'like', "%{$q}%")
                    ->orWhere('model_type', 'like', "%{$q}%")
                    ->orWhere('ip_address', 'like', "%{$q}%");
            });
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter action prefix
        if ($request->filled('action')) {
            $query->where('action', 'like', $request->action . '%');
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        $actionGroups = [
            'request' => 'Requests',
            'approval' => 'Approvals',
            'user' => 'Users',
            'division' => 'Divisions',
            'policy' => 'Policies',
            'role' => 'Roles',
            'travel' => 'Travel Zones',
            'client' => 'Client Codes',
            'auth' => 'Auth (Login/Logout)',
        ];

        return view('settings.audit_logs.index', compact('logs', 'users', 'actionGroups'));
    }
}
