<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalFlow extends Model
{
    protected $guarded = [];

    public function steps()
    {
        return $this->hasMany(ApprovalStep::class)->orderBy('step_order');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
