<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveAddOn;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class LeaveAddOnService extends Controller
{
    public function construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function addLeaveBalance(Array $data)
    {
        $leave_balance = LeaveBalance::where('user_id', '=', $data['user_id'])
            ->where('leave_type_id', $data['leave_type_id'])
            ->first();

        if ( ! $leave_balance ) {
            return ['message' => 'Leave Balance not found.'];
        }

        $leave_balance->addBalance($data['added_qty']);

        $data = array_merge(
            $data,
            ['leave_balance_id' => $leave_balance->id],
            ['new_balance' => $leave_balance->balance],
        );

        $message = (LeaveAddOn::create($data))
            ? 'New Leave Balance successfully updated.'
            : 'An error has occured during updating leave balance.';

        return ['message' => $message];
    }
}
