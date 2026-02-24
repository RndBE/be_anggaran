<?php

namespace App\Services;

use App\Models\Request;
use App\Models\ApprovalStep;
use App\Models\ApprovalFlow;
use App\Models\User;

class WorkflowService
{
    public function determineFlow(Request $request): ApprovalFlow
    {
        // Entertain → Director Approval Flow, else → Standard Flow
        $hasEntertain = $request->items()->where('type', 'entertain')->exists();

        if ($hasEntertain) {
            return ApprovalFlow::where('name', 'Director Approval Flow')->first()
                ?? ApprovalFlow::first();
        }

        return ApprovalFlow::where('name', 'Standard Flow')->first()
            ?? ApprovalFlow::first();
    }

    /**
     * Get the next pending ApprovalStep for a request.
     *
     * A division-level step is skipped when ANY of these is true:
     *  1. Submitter's own level is already >= required rank
     *     (submitter.level <= step.required_level  →  peer/self approval, pointless)
     *  2. No user exists in the submitter's division with an eligible level
     *     (no one to approve → step would be permanently stuck)
     *
     * Examples:
     *  - Staff (level 4) in IT submits, Step L≤3 → check if IT has any L1/2/3 user
     *  - Supervisor (level 3) submits → Step L≤3 skipped (rule #1), Step L≤2 checked
     *  - Division with no Manager → Step L≤2 auto-skipped (rule #2)
     */
    public function getNextApproverStep(Request $request): ?ApprovalStep
    {
        $flow = $this->determineFlow($request);
        $submitter = $request->user;

        // Already-approved step IDs
        $approvedStepIds = $request->approvals()
            ->where('status', 'approved')
            ->pluck('approval_step_id');

        return $flow->steps()
            ->orderBy('step_order')
            ->get()
            ->first(function (ApprovalStep $step) use ($approvedStepIds, $submitter) {

                // Already approved → skip
                if ($approvedStepIds->contains($step->id)) {
                    return false;
                }

                // Division-level step checks
                if ($step->isDivisionLevel()) {

                    // Rule 1: submitter's rank already covers this step
                    if (
                        $submitter && $submitter->level !== null
                        && $submitter->level <= $step->required_level
                    ) {
                        return false;
                    }

                    // Rule 2: no eligible approver exists in the division
                    if ($submitter && $submitter->division_id) {
                        $hasEligibleApprover = User::where('division_id', $submitter->division_id)
                            ->where('level', '<=', $step->required_level)
                            ->where('id', '!=', $submitter->id) // must be someone else
                            ->exists();

                        if (!$hasEligibleApprover) {
                            return false;
                        }
                    }
                }

                // Role+level step (e.g. Manager Finance)
                if ($step->isRoleLevel()) {
                    // Rule 3: skip if no user has both the required role AND sufficient level
                    $hasEligibleApprover = User::whereHas('roles', fn($q) => $q->where('id', $step->role_id))
                        ->where('level', '<=', $step->required_level)
                        ->exists();

                    if (!$hasEligibleApprover) {
                        return false;
                    }
                }

                return true;
            });
    }
}
