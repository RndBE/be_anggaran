<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelReportApproval extends Model
{
    protected $guarded = [];

    public function travelReport()
    {
        return $this->belongsTo(TravelReport::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Link to the dynamic approval step from approval_steps table.
     */
    public function approvalStep()
    {
        return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    }

    /**
     * Get the human-readable label for this step.
     */
    public function getStepLabelAttribute(): string
    {
        if ($this->approvalStep) {
            $step = $this->approvalStep;
            if ($step->isDivisionLevel()) {
                return 'Level ≤ ' . $step->required_level . ' (Divisi)';
            } elseif ($step->isRoleLevel()) {
                return ($step->role?->name ?? '—') . ' (Level ≤ ' . $step->required_level . ')';
            } else {
                return $step->role?->name ?? '—';
            }
        }

        // Legacy fallback for old hardcoded steps
        $legacyLabels = [
            'level3' => 'Supervisor (Level 3)',
            'level2' => 'Manager (Level 2)',
            'k3' => 'K3',
            'hrd' => 'HRD',
            'finance' => 'Finance',
        ];
        return $legacyLabels[$this->step] ?? $this->step ?? '—';
    }
}
