<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCarryForward extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'leave_type_id',
        'start_period',
        'end_period',
        'amount',
    ];

    public function leave()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
