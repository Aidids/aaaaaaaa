<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'pic_id',
        'new_balance',
        'leave_balance_id',
        'added_qty',
        'remark',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personInCharge()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function leaveBalance()
    {
        return $this->belongsTo(LeaveBalance::class, 'leave_balance_id');
    }
}
