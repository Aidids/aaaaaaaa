<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CompassionateLeaveConsoleService extends Controller
{
    public function __construct()
    {
        $this->CompassionateLeaveService = new CompassionateLeaveService();
    }

    public function addCompassionateLeaveType()
    {
        $leaveType = LeaveType::firstOrCreate([
            'name' => 'Compassionate Leave',
            'amount' => 0,
            'description' => 'Dayang\'s Kuala Lumpur headquarters compassionate leave type',
        ]);

        if($leaveType) {
            Log::info('Compassionate leave type creation success');
            return $leaveType;
        }
        else {
            Log::info('Compassionate leave type creation failed');
            return false;
        }
    }

    public function createCompassionateLeaveBalance()
    {
        $leaveBalance = 0;
        $compassionateLeaveType = LeaveType::getCompassionateLeaveType();

        if ($compassionateLeaveType) {
            $allUser = User::select('id')->orderBy('id')->get();

            foreach ($allUser as $user) {
                $check = LeaveBalance::where([
                    'user_id' => $user->id,
                    'leave_type_id' => $compassionateLeaveType->id,
                ])->first();

                if(!$check) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $compassionateLeaveType->id,
                        'proRated' => 0,
                        'entitlement' => 0,
                        'carry_forward' => 0,
                        'balance' => 0,
                        'total' => 0,
                    ]);
                    $leaveBalance++;
                }
            }
            Log::info($leaveBalance . ' New record of Compassionate leave balance created');
        }

        return $leaveBalance;
    }
}
