<?php

namespace App\Services;

use App\Enums\LeaveRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\RedeemReplacementLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RedeemReplacementLeaveConsoleService extends Controller
{
    public function __construct()
    {
        $this->RedeemReplacementLeaveService = new RedeemReplacementLeaveService();
    }

    public function addReplacementLeaveType()
    {
        $leaveType = LeaveType::firstOrCreate([
            'name' => 'Replacement Leave',
            'amount' => 0,
            'description' => 'Dayang\'s Kuala Lumpur headquarters replacement leave type',
        ]);

        if($leaveType) {
            Log::info('Replacement leave type creation success');
            return $leaveType;
        }
        else {
            Log::info('Replacement leave type creation failed');
            return false;
        }
    }

    public function createReplacementLeaveBalance()
    {
        $leaveBalance = 0;
        $replacementLeaveType = LeaveType::getReplacementLeaveType();

        if ($replacementLeaveType) {
            $allUser = User::select('id')->orderBy('id')->get();

            foreach ($allUser as $user) {
                $check = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $replacementLeaveType->id,
                ])->first();

                if(!$check) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $replacementLeaveType->id,
                        'proRated' => 0,
                        'entitlement' => 0,
                        'carry_forward' => 0,
                        'balance' => 0,
                        'total' => 0,
                    ]);
                    $leaveBalance++;
                }
            }
            Log::info($leaveBalance . ' New record of Replacement leave balance created');
        }

        return $leaveBalance;
    }

    public function replacementLeaveExpired()
    {
        $expiredReplacementRecords = RedeemReplacementLeave::where('expired_date', $this->RedeemReplacementLeaveService->now->toDateString())
            ->get();

        foreach ($expiredReplacementRecords as $expiredReplacement) {
            $leaveRequest = LeaveRequest::find($expiredReplacement->leave_request_id);

            $leaveBalance = $this->RedeemReplacementLeaveService->getLeaveBalance($expiredReplacement->user_id);

            if (!$leaveRequest){
                $this->notClaimedReplacement($expiredReplacement, $leaveBalance);
                continue;
            }

            if (! $leaveRequest->calculated) {
                $leaveRequest->update(['overall_status' => LeaveRequestStatus::expired->value]);

                $this->notClaimedReplacement($expiredReplacement, $leaveBalance);
            }
            else {
                $this->claimedCalculated($expiredReplacement, $leaveBalance);
            }
        }
    }

    private function notClaimedReplacement(RedeemReplacementLeave $replacement, LeaveBalance $leaveBalance)
    {
        $leaveBalance->update([
            'balance' => $leaveBalance->deductExpired(
                $leaveBalance->balance, $replacement->added_qty),
        ]);
        $replacement->update([
            'overall_status' => LeaveRequestStatus::expired->value,
            'balance_qty' => 0,
        ]);
    }

    private function claimedCalculated(RedeemReplacementLeave $replacement, LeaveBalance $leaveBalance)
    {
        // deduct leave balance->balance based on replacement balance_qty
        $newBalance = $leaveBalance->deductExpired($leaveBalance->balance, $replacement->balance_qty);
        $leaveBalance->update(['balance' => $newBalance]);

        // set replacement status to expired, balance dont set to 0
        $replacement->update(['overall_status' => LeaveRequestStatus::expired->value]);
    }
}
