<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelReportApproval extends Model
{
    protected $guarded = [];

    /**
     * Ordered steps for LHP approval chain.
     * key => [label, type: 'level'|'role', value: level number or role slug]
     */
    public const STEPS = [
        'level3' => ['label' => 'Supervisor (Level 3)', 'type' => 'level', 'value' => 3, 'order' => 1],
        'level2' => ['label' => 'Manager (Level 2)', 'type' => 'level', 'value' => 2, 'order' => 2],
        'k3' => ['label' => 'K3', 'type' => 'role', 'value' => 'k3', 'order' => 3],
        'hrd' => ['label' => 'HRD', 'type' => 'role', 'value' => 'hrd', 'order' => 4],
        'finance' => ['label' => 'Finance', 'type' => 'role', 'value' => 'finance', 'order' => 5],
    ];

    public function travelReport()
    {
        return $this->belongsTo(TravelReport::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function getStepLabelAttribute(): string
    {
        return self::STEPS[$this->step]['label'] ?? $this->step;
    }
}
