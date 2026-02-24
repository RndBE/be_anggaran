<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalStep extends Model
{
    protected $guarded = [];

    public function flow()
    {
        return $this->belongsTo(ApprovalFlow::class, 'approval_flow_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'approval_step_id');
    }

    /**
     * True if this step requires a specific level from the requester's division.
     * (role_id must be null — pure division-level step)
     */
    public function isDivisionLevel(): bool
    {
        return is_null($this->role_id) && !is_null($this->required_level);
    }

    /**
     * True if this step requires a specific role AND a minimum level.
     * e.g. Finance role (role_id set) + Manager level (required_level=2)
     */
    public function isRoleLevel(): bool
    {
        return !is_null($this->role_id) && !is_null($this->required_level);
    }
}
