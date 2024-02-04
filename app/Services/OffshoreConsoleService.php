<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;

class OffshoreConsoleService extends Controller
{
    public function __construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function resetOffshoreBalance()
    {
        $count = 0;
        $offshoreLeaveType = LeaveType::where('name', 'Offshore Leave')->first();

        $today = Carbon::today();
        $endOfYearDate = $today->endOfYear();

        if ($endOfYearDate->format('m-d') === '12-31') {
            $offshoreBalances = LeaveBalance::where('leave_type_id', $offshoreLeaveType->id)
                ->get();

            foreach ($offshoreBalances as $userOffshore) {
                $userOffshore->update(['balance' => 0]);

                $count++;
            }
        }

        return $count;
    }
}
