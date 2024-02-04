<?php

namespace App\Services;

use App\Enums\LeaveRequestStatus;
use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveDeductionHistory;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class LeaveDeductionHistoryService extends Controller
{
    public function deductLeaveHistory(array $data)
    {
        if (array_key_exists('deduct_all', $data)) {
            $usersLeaveBalance = LeaveBalance::with('user')
                ->where('leave_type_id', $data['leave_type_id'])
                ->get();
        }
        else {
            $usersLeaveBalance = LeaveBalance::with('user')
                ->where([
                    'leave_type_id' => 1,
                    'user_id' => $data['user_id'],
                    ])->get();

            $data = array_merge($data, ['deduct_all' => 0]);
        }

        $leaveRequestCreated = $this->createLeaveRequest($usersLeaveBalance->toArray(), $data);

        if($leaveRequestCreated) {
            $this->storeLeaveHistoryDeduction($usersLeaveBalance->toArray(), $data);

            Log::info('Leave Deduct History Creation Success');
            Artisan::call('leave:deduction');

            return [
                'message' => 'Leave Deduction History Success.',
                'status' => 200
            ];
        }
        else {
            Log::info('Leave Deduct History Creation Failed');

            return [
                'message' => 'Leave Deduction History Failed.',
                'status' => 500
            ];
        }
    }

    private function createLeaveRequest(array $usersLeaveBalance, Array $data): bool
    {
        //chunk collection of users into group of 50
        $chunks = array_chunk($usersLeaveBalance, 50);

        foreach ($chunks as $chunk) {
            $leaveRequestData = [];

            foreach ($chunk as $userBalance) {
                $userBalance = collect($userBalance);

                $leaveRequestData[] = [
                    'user_id' => $userBalance['user']['id'],
                    'leave_balance_id' => $userBalance['id'],
                    'start_date' => $data['hr_startDate'],
                    'start_date_type' => 'full day',
                    'end_date' => $data['hr_endDate'],
                    'end_date_type' => 'full day',
                    'duration' => $data['duration'],
                    'reason' => 'Applied by HR: ' . $data['remark'],
                    'overall_status' => LeaveRequestStatus::approved->value,
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }
            $lrCreated = LeaveRequest::insert($leaveRequestData);

            if (!$lrCreated) {
                return false;
            }
        }

        return true;
    }

    private function storeLeaveHistoryDeduction(array $users, array $data)
    {
        Log::info('start chunk process');

        //chunk collection of users into group of 50
        $chunks = array_chunk($users, 50);

        foreach ($chunks as $chunk) {
            $leaveDeductionHistories = [];

            foreach ($chunk as $user) {
                $user = collect($user);

                $leaveDeductionHistories[] = [
                    'user_id' => $user['user']['id'],
                    'remark' => $data['remark'],
                    'duration' => $data['duration'],
                    'hr_ic_id' => $data['hr_id'],
                    'hr_ic_date' => $data['hr_startDate'],
                    'deduct_all' => $data['deduct_all'],
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ];
            }

            LeaveDeductionHistory::insert($leaveDeductionHistories);
        }

        Log::info('END chunk process');
    }
}
