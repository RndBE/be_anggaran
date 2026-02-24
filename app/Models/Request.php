<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clientCode()
    {
        return $this->belongsTo(ClientCode::class);
    }

    public function items()
    {
        return $this->hasMany(RequestItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
