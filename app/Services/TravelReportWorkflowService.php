<?php

namespace App\Services;

use App\Models\TravelReport;
use App\Models\TravelReportApproval;
use App\Models\User;

class TravelReportWorkflowService
{
    /**
     * The full ordered step chain.
     */
    private const STEP_CHAIN = ['level3', 'level2', 'k3', 'hrd', 'finance'];

    /**
     * Get the next step key to create approval for, or null if chain is complete.
     * Applies skip rules:
     *   - Level-based steps: skip if submitter's level is <= the step's required level
     *     (submitter IS already at that level or higher — self/peer approval is pointless)
     *   - Role-based steps: skip if no user has the required role
     */
    public function getNextStep(TravelReport $report): ?string
    {
        $submitter = $report->user;

        $approvedSteps = $report->approvals()
            ->where('status', 'approved')
            ->pluck('step')
            ->toArray();

        foreach (self::STEP_CHAIN as $stepKey) {
            // Already approved — skip
            if (in_array($stepKey, $approvedSteps)) {
                continue;
            }

            $stepDef = TravelReportApproval::STEPS[$stepKey];

            if ($stepDef['type'] === 'level') {
                $requiredLevel = $stepDef['value'];

                // Skip if submitter is already at this level or higher (lower number = higher rank)
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

            if ($stepDef['type'] === 'role') {
                // Skip if no user has the required role
                $roleSlug = $stepDef['value'];
                $eligible = User::whereHas('roles', fn($q) => $q->where('slug', $roleSlug))->exists();
                if (!$eligible) {
                    continue;
                }
            }

            return $stepKey;
        }

        return null; // Chain complete
    }

    /**
     * Determine if a user can process a given approval based on step type.
     */
    public function canApprove(User $user, TravelReportApproval $approval): bool
    {
        $stepDef = TravelReportApproval::STEPS[$approval->step] ?? null;
        if (!$stepDef)
            return false;

        if ($stepDef['type'] === 'level') {
            $requiredLevel = $stepDef['value'];
            $submitter = $approval->travelReport->user;

            return $user->division_id === $submitter->division_id
                && $user->level !== null
                && $user->level <= $requiredLevel
                && $user->id !== $submitter->id;
        }

        if ($stepDef['type'] === 'role') {
            return $user->hasRole($stepDef['value']);
        }

        return false;
    }

    /**
     * Get the step_order for a given step key.
     */
    public function getStepOrder(string $stepKey): int
    {
        return TravelReportApproval::STEPS[$stepKey]['order'] ?? 99;
    }
}
