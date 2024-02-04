<?php

namespace App\Models;

use App\Observers\ReplacementLeaveObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemReplacementLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'leave_request_id',
        'start_date',
        'end_date',
        'remark',

        'first_approver_id',
        'first_approver_status',
        'first_approver_remark',
        'first_approver_date',

        'second_approver_id',
        'second_approver_status',
        'second_approver_remark',
        'second_approver_date',

        'hr_ic_id',
        'hr_ic_status',
        'hr_ic_remark',
        'hr_ic_date',
        'added_qty',
        'balance_qty',

        'overall_status',
        'expired_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function firstApprover()
    {
        return $this->belongsTo(User::class, 'first_approver_id');
    }

    public function secondApprover()
    {
        return $this->belongsTo(User::class, 'second_approver_id');
    }

    public function hrIncharge()
    {
        return $this->belongsTo(User::class, 'hr_ic_id');
    }

    public function attachment()
    {
        return $this->hasMany(ReplacementAttachment::class);
    }

    public static function getSelectedRedemption(int $leaveRequestID)
    {
        $redeem = self::where('leave_request_id', $leaveRequestID)->first();

        if(!$redeem) {
            return null;
        }

        return ('Balance: ' . $redeem->balance_qty . ' Day\'s | Expired Date: ' . Carbon::parse($redeem->expired_date)->format('d M Y'));
    }
}
