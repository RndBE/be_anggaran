<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelReport extends Model
{
    protected $guarded = [];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'surat_tugas_date' => 'date',
        'results' => 'array',
        'recommendations' => 'array',
    ];

    public function approvals()
    {
        return $this->hasMany(TravelReportApproval::class)->orderBy('step_order');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function activities()
    {
        return $this->hasMany(TravelReportActivity::class)->orderBy('sort_order');
    }

    public function documents()
    {
        return $this->hasMany(TravelReportDocument::class)->orderBy('sort_order');
    }

    /**
     * Get the job position label from user's roles.
     */
    public function getJobPositionAttribute()
    {
        $roles = $this->user?->roles;
        if ($roles && $roles->isNotEmpty()) {
            return $roles->pluck('name')->implode(', ');
        }
        return 'Staff';
    }

    /**
     * Get the division name from related user.
     */
    public function getDivisionNameAttribute()
    {
        return $this->user?->division?->name ?? '-';
    }

    /**
     * Calculate trip duration in days.
     */
    public function getDurationDaysAttribute()
    {
        if ($this->departure_date && $this->return_date) {
            return $this->departure_date->diffInDays($this->return_date) + 1;
        }
        return 0;
    }
}
