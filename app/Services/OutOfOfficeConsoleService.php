<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OutOfOfficeConsoleService extends Controller
{
    public function createOutOfOfficeLeaveType(): void
    {
        $check = LeaveType::where([
            'name' => 'Out Of Office Leave',
            'amount' => 365,
        ])->first();

        if ($check) {
            Log::info('Out Of Office leave type, already exists');
        }
        else {
            $leaveCreated =  LeaveType::create([
                'name' => 'Out Of Office Leave',
                'amount' => 365,
                'description' => 'Dayang\'s Kuala Lumpur headquarters out of office leave type',
            ]);
            ($leaveCreated) && Log::info('Out Of Office leave type creation success');
        }
    }

    public function createOutOfOfficeLeaveBalance()
    {
        $newRecordCounter = 0;
        $OutOfOfficeLeaveType = LeaveType::getOutOfOfficeLeaveType();

        if ($OutOfOfficeLeaveType) {

            foreach(User::all() as $user) {
                $checkExisting = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $OutOfOfficeLeaveType->id,
                ])->first();

                if (!$checkExisting) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $OutOfOfficeLeaveType->id,
                        'proRated' => 0,
                        'entitlement' => 0,
                        'carry_forward' => 0,
                        'balance' => 365,
                        'total' => 365,
                    ]);
                }
                $newRecordCounter++;
            }
            Log::info($newRecordCounter . ' New record of Out Of Office leave balance created');
        }

        return $newRecordCounter;
    }
}
