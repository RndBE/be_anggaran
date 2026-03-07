<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelReportApproval;
use App\Models\TravelReport;
use App\Services\TravelReportWorkflowService;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TravelReportApprovalController extends Controller
{
    protected TravelReportWorkflowService $workflow;

    public function __construct(TravelReportWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * List all pending LHP approvals for the current user.
     */
    public function index()
    {
        $user = Auth::user();

        // Load all pending LHP approvals, then filter by canApprove
        $pending = TravelReportApproval::where('status', 'pending')
            ->with(['travelReport.user.division', 'travelReport.user.roles'])
            ->get()
            ->filter(fn($approval) => $this->workflow->canApprove($user, $approval))
            ->values();

        // Also show approvals already processed by this user
        $processed = TravelReportApproval::where('approver_id', $user->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->with(['travelReport.user'])
            ->latest('updated_at')
            ->take(20)
            ->get();

        return view('travel-report-approvals.index', compact('pending', 'processed'));
    }

    /**
     * Show a single approval for review.
     */
    public function show(TravelReportApproval $travelReportApproval)
    {
        $user = Auth::user();

        if (!$this->workflow->canApprove($user, $travelReportApproval)) {
            abort(403, 'Anda tidak berhak memproses approval ini.');
        }

        $travelReportApproval->load([
            'travelReport.user.division',
            'travelReport.user.roles',
            'travelReport.activities.documents',
            'travelReport.approvals.approver',
        ]);

        return view('travel-report-approvals.show', compact('travelReportApproval'));
    }

    /**
     * Approve or reject a travel report.
     */
    public function update(Request $request, TravelReportApproval $travelReportApproval)
    {
        $user = Auth::user();

        if (!$this->workflow->canApprove($user, $travelReportApproval)) {
            abort(403, 'Anda tidak berhak memproses approval ini.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'comments' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $travelReportApproval->update([
                'status' => $request->status,
                'comments' => $request->comments,
                'approver_id' => $user->id,
            ]);

            $travelReport = $travelReportApproval->travelReport;

            if ($request->status === 'approved') {
                // Determine next step
                $nextStep = $this->workflow->getNextStep($travelReport);

                if ($nextStep) {
                    TravelReportApproval::create([
                        'travel_report_id' => $travelReport->id,
                        'step' => $nextStep,
                        'step_order' => $this->workflow->getStepOrder($nextStep),
                        'status' => 'pending',
                    ]);
                } else {
                    // All steps done — mark approved
                    $travelReport->update(['approval_status' => 'approved']);
                }
            } else {
                // Rejected
                $travelReport->update(['approval_status' => 'rejected']);
            }

            DB::commit();

            AuditLogService::log('travel_report_approval.' . $request->status, $travelReport, [
                'step' => $travelReportApproval->step,
                'comments' => $request->comments,
            ]);

            return redirect()->route('travel-report-approvals.index')
                ->with('success', 'LHP berhasil ' . ($request->status === 'approved' ? 'disetujui' : 'ditolak') . '.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
