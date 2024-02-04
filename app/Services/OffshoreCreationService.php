<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OffshoreCreationService extends Controller
{
    public function addOffshoreLeaveType()
    {
        $leaveType = LeaveType::firstOrCreate([
            'name' => 'Offshore Leave',
            'amount' => 0,
            'description' => 'Dayang\'s Kuala Lumpur headquarters offshore leave type',
        ]);

        if($leaveType) {
            Log::info('Offshore leave type creation success');
            return $leaveType;
        }
        else {
            Log::info('Offshore leave type creation failed');
            return false;
        }
    }

    public function createUserOffshoreLeaveBalance()
    {
        $leaveBalance = 0;
        $offshoreLeaveType = LeaveType::where('name', 'Offshore Leave')->first();

        if ($offshoreLeaveType) {
            $allUser = User::select('id')->orderBy('id')->get();

            foreach ($allUser as $user) {
                $check = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $offshoreLeaveType->id,
                ])->first();

                if(!$check) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $offshoreLeaveType->id,
                        'proRated' => 0,
                        'entitlement' => 0,
                        'carry_forward' => 0,
                        'balance' => 0,
                        'total' => 0,
                    ]);
                    $leaveBalance++;
                }
            }
            Log::info($leaveBalance . ' New record of Offshore leave balance created');
        }

        return $leaveBalance;
    }
}
