<?php

namespace App\Services;

use App\Models\ApprovalFlow;
use App\Models\ApprovalStep;
use App\Models\TravelReport;
use App\Models\TravelReportApproval;
use App\Models\User;

class TravelReportWorkflowService
{
    /**
     * Get the active LHP approval flow from the database.
     * Falls back to null if none is configured.
     */
    public function getActiveFlow(): ?ApprovalFlow
    {
        return ApprovalFlow::where('flow_type', 'lhp')
            ->where('is_active', true)
            ->with('steps.role')
            ->first();
    }

    /**
     * Get the next step to create approval for, or null if chain is complete.
     * Reads from DB-configured flow. Applies skip rules:
     *   - Division-level steps: skip if submitter's level <= required level
     *   - Role-based steps: skip if no user has the required role
     */
    public function getNextStep(TravelReport $report): ?ApprovalStep
    {
        $flow = $this->getActiveFlow();
        if (!$flow) {
            return null; // No flow configured
        }

        $submitter = $report->user;

        // Get step IDs that are already approved
        $approvedStepIds = $report->approvals()
            ->where('status', 'approved')
            ->pluck('approval_step_id')
            ->toArray();

        foreach ($flow->steps as $step) {
            // Already approved — skip
            if (in_array($step->id, $approvedStepIds)) {
                continue;
            }

            // Division-level step (no role, just level requirement)
            if ($step->isDivisionLevel()) {
                $requiredLevel = $step->required_level;

                // Skip if submitter is already at this level or higher
                if ($submitter && $submitter->level !== null && $submitter->level <= $requiredLevel) {
                    continue;
                }

                // Skip if no eligible approver exists in the same division
                if ($submitter && $submitter->division_id) {
                    $eligible = User::where('division_id', $submitter->division_id)
                        ->where('level', '<=', $requiredLevel)
                        ->where('id', '!=', $submitter->id)
                        ->exists();
                    if (!$eligible) {
                        continue;
                    }
                }
            }

            // Role-level step (with or without level constraint)
            if ($step->role_id) {
                $roleSlug = $step->role->slug ?? null;
                if ($roleSlug) {
                    $query = User::whereHas('roles', fn($q) => $q->where('slug', $roleSlug));

                    // If also has level requirement (isRoleLevel)
                    if ($step->isRoleLevel()) {
                        $query->where('level', '<=', $step->required_level);
                    }

                    if (!$query->exists()) {
                        continue;
                    }
                }
            }

            return $step; // This is the next pending step
        }

        return null; // Chain complete
    }

    /**
     * Determine if a user can process a given approval based on step definition.
     */
    public function canApprove(User $user, TravelReportApproval $approval): bool
    {
        $step = $approval->approvalStep;
        if (!$step) {
            return false;
        }

        // Division-level step
        if ($step->isDivisionLevel()) {
            $submitter = $approval->travelReport->user;
            return $user->division_id === $submitter->division_id
                && $user->level !== null
                && $user->level <= $step->required_level
                && $user->id !== $submitter->id;
        }

        // Role-based step (with or without level)
        if ($step->role_id) {
            $hasRole = $user->hasRole($step->role->slug);
            if ($step->isRoleLevel()) {
                return $hasRole && $user->level !== null && $user->level <= $step->required_level;
            }
            return $hasRole;
        }

        return false;
    }
}
