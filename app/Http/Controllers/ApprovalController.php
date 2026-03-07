<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Approval;
use App\Models\Request as BudgetRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogService;

class ApprovalController extends Controller
{
    /**
     * Check if $user can process the given $approval step.
     * Supports both role-based and division-level-based steps.
     */
    private function canApprove(User $user, Approval $approval): bool
    {
        $step = $approval->step;

        if ($step->isDivisionLevel()) {
            // Division-level step: same division + level <= required_level
            $requesterDivId = $approval->request->user->division_id;
            return $user->division_id === $requesterDivId
                && $user->level !== null
                && $user->level <= $step->required_level;
        }

        if ($step->isRoleLevel()) {
            // Role + level: user must have the role AND level <= required_level
            // e.g. Finance role at Manager level (level <= 2)
            return $step->role !== null
                && $user->hasRole($step->role->slug)
                && $user->level !== null
                && $user->level <= $step->required_level;
        }

        // Pure role-based step
        return $step->role !== null && $user->hasRole($step->role->slug);
    }

    public function index()
    {
        $user = Auth::user();
        $roleIds = $user->roles->pluck('id');

        // Fetch pending approvals where user can process the step:
        // either via matching role, or matching division+level
        $approvals = Approval::where('status', 'pending')
            ->whereHas('step', function ($q) use ($user, $roleIds) {
                $q->where(function ($sub) use ($roleIds) {
                    // Role-based steps
                    $sub->whereNotNull('role_id')->whereIn('role_id', $roleIds);
                })->orWhere(function ($sub) use ($user) {
                    // Division-level steps
                    if ($user->division_id && $user->level) {
                        $sub->whereNull('role_id')
                            ->whereNotNull('required_level')
                            ->where('required_level', '>=', $user->level)
                            ->whereHas('approvals', function ($aq) use ($user) {
                                $aq->whereHas('request', function ($rq) use ($user) {
                                    $rq->whereHas('user', function ($uq) use ($user) {
                                        $uq->where('division_id', $user->division_id);
                                    });
                                });
                            });
                    }
                })->orWhere(function ($sub) use ($user, $roleIds) {
                    // Role + level steps (Finance Manager etc.)
                    if ($user->level) {
                        $sub->whereNotNull('role_id')
                            ->whereIn('role_id', $roleIds)
                            ->whereNotNull('required_level')
                            ->where('required_level', '>=', $user->level);
                    }
                });
            })
            ->with('request')
            ->get();

        return view('approvals.index', compact('approvals'));
    }

    public function show(Approval $approval)
    {
        $user = Auth::user();

        if (!$this->canApprove($user, $approval)) {
            abort(403, 'Unauthorized action. You do not have the required role/level to view this approval.');
        }

        $approval->load(['request.items.attachments', 'request.clientCode']);
        return view('approvals.show', compact('approval'));
    }

    public function update(Request $request, Approval $approval)
    {
        $user = Auth::user();

        if (!$this->canApprove($user, $approval)) {
            abort(403, 'Unauthorized action. You do not have the required role/level to update this approval.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected,revision',
            'comments' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $approval->update([
                'status' => $request->status,
                'comments' => $request->comments,
                'approver_id' => Auth::id()
            ]);

            $parentRequest = $approval->request;

            if ($request->status === 'approved') {
                $workflow = app(\App\Services\WorkflowService::class);
                $nextStep = $workflow->getNextApproverStep($parentRequest);

                if ($nextStep) {
                    Approval::create([
                        'request_id' => $parentRequest->id,
                        'approval_step_id' => $nextStep->id,
                        'status' => 'pending'
                    ]);
                } else {
                    $parentRequest->update(['status' => 'approved']);
                }
            } else {
                $parentRequest->update([
                    'status' => $request->status === 'revision' ? 'revision_requested' : 'rejected',
                    'rejection_reason' => $request->comments
                ]);
            }

            DB::commit();
            $action = 'approval.' . $request->status; // approval.approved / approval.rejected / approval.revision
            AuditLogService::log($action, $approval->request, [
                'approval_id' => $approval->id,
                'comments' => $request->comments,
            ]);
            return redirect()->route('approvals.index')->with('success', 'Approval status updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process approval: ' . $e->getMessage()]);
        }
    }
}
