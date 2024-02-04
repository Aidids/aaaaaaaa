<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'balance',
        'total',
        'taken',
        'proRated',
        'entitlement',
        'carry_forward',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leave()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveDeduction($duration = 0): void
    {
        $carryForward = $this->carry_forward;

        if ($carryForward > 0) {
            if ($duration <= $carryForward) { // Deduct cf only
                $this->decrement('carry_forward', $duration);
                $this->decrement('balance', $duration);
            }
            else { // Deduct cf and balance
                $this->decrement('carry_forward', $carryForward);
                $this->decrement('balance', $duration);
            }
        }
        else { // Deduct balance only
            $this->decrement('balance', $duration);
        }

        $this->increment('taken', $duration);
    }

    public function addBalance($duration = 0): void
    {
        $this->increment('balance', $duration);
        $this->increment('total', $duration);
    }

    public function cancelCalculated($duration = 0): void
    {
        $this->increment('balance', $duration);
        $this->decrement('taken', $duration);
    }

    public function deductExpired($balance, $expiredQuantity = 0)
    {
        $balance = $balance - $expiredQuantity;

        // if balance is -ve after deduct, change balance into 0
        return max(0, $balance);
    }

    public function compassionateTaken($duration = 0): void
    {
        $this->increment('taken', $duration);
    }

    public function checkBalance(LeaveBalance $leaveBalance, $duration = 0): bool
    {
        return $leaveBalance->balance >= $duration;
    }
}
