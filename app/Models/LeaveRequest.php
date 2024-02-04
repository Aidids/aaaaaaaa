<?php

namespace App\Models;

use App\Observers\LeaveRequestObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'leave_balance_id',
        'start_date',
        'start_date_type',
        'end_date',
        'end_date_type',
        'duration',
        'deduct_type',
        'compassionate_type',
        'reason',
        'file',
        'first_approver_id',
        'first_approver_status',
        'first_approver_remark',
        'first_approver_date',
        'second_approver_id',
        'second_approver_status',
        'second_approver_remark',
        'second_approver_date',
        'overall_status',
        'calculated',
        'hr_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveBalance()
    {
        return $this->belongsTo(LeaveBalance::class, 'leave_balance_id');
    }

    public function firstApprover()
    {
        return $this->belongsTo(User::class, 'first_approver_id');
    }

    public function secondApprover()
    {
        return $this->belongsTo(User::class, 'second_approver_id');
    }

    public function attachment()
    {
        return $this->hasMany(LeaveRequestAttachment::class);
    }

    public function replacementCoupon()
    {
        return $this->hasOne(RedeemReplacementLeave::class);
    }
}
