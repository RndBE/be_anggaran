<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function requestItem()
    {
        return $this->belongsTo(RequestItem::class);
    }
}
