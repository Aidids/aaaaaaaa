<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_request_id',
        'path',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }
}
