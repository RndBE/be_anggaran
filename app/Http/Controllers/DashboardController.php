<?php

namespace App\Http\Controllers;

use App\Models\Request as BudgetRequest;
use App\Models\Approval;
use App\Models\RequestItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── My Requests (for non-admin) ───────────────────────────────────────
        $myRequests = BudgetRequest::where('user_id', $user->id)
            ->selectRaw("status, COUNT(*) as count, COALESCE(SUM(total_amount),0) as total")
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $myTotal = BudgetRequest::where('user_id', $user->id)->sum('total_amount');
        $myCount = BudgetRequest::where('user_id', $user->id)->count();

        // ── System-wide stats (for admin / managers) ─────────────────────────
        $canManage = $user->hasPermission('settings.manage') || $user->hasPermission('reports.view');

        $systemStats = [];
        if ($canManage) {
            $systemStats = [
                'total_requests' => BudgetRequest::count(),
                'pending' => BudgetRequest::whereIn('status', ['submitted', 'pending'])->count(),
                'approved' => BudgetRequest::where('status', 'approved')->count(),
                'rejected' => BudgetRequest::where('status', 'rejected')->count(),
                'revision' => BudgetRequest::where('status', 'revision_requested')->count(),
                'total_amount' => BudgetRequest::sum('total_amount'),
                'approved_amount' => BudgetRequest::where('status', 'approved')->sum('total_amount'),
                'total_users' => User::count(),
            ];
        }

        // ── Pending approvals for this user ───────────────────────────────────
        $roleIds = $user->roles->pluck('id');
        $myPendingApprovals = Approval::where('status', 'pending')
            ->whereHas('step', fn($q) => $q->whereIn('role_id', $roleIds))
            ->with('request')
            ->latest()
            ->take(5)
            ->get();

        // ── Spending by category (last 6 months) ──────────────────────────────
        $spendingByType = RequestItem::join('requests', 'request_items.request_id', '=', 'requests.id')
            ->where('requests.status', 'approved')
            ->select('request_items.type', DB::raw('SUM(request_items.amount) as total'))
            ->groupBy('request_items.type')
            ->orderByDesc('total')
            ->get();

        // ── Monthly trend (last 6 months) ─────────────────────────────────────
        $monthlyTrend = BudgetRequest::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count,
                COALESCE(SUM(total_amount), 0) as total
            ")
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill in missing months with zeros
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $months->put($key, $monthlyTrend->get($key, (object) ['month' => $key, 'count' => 0, 'total' => 0]));
        }

        // ── My recent requests ────────────────────────────────────────────────
        $recentRequests = BudgetRequest::where('user_id', $user->id)
            ->with('clientCode')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'myRequests',
            'myTotal',
            'myCount',
            'systemStats',
            'canManage',
            'myPendingApprovals',
            'spendingByType',
            'months',
            'recentRequests'
        ));
    }
}
