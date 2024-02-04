<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Style\Style;
use Rap2hpoutre\FastExcel\FastExcel;

class ExcelApi extends Controller
{
    public function __construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
        $this->leaveRequestService = new LeaveRequestService();
    }

    public function downloadLeaveSummary (Request $request)
    {
        $data = $request->toArray();

        if(!empty($data)) {
            $leaveSummary = $this->leaveRequestService->getLeaveSummary($data, is_excel: true);
        }
        else {
            $leaveSummary = LeaveRequest::with(['user', 'leaveBalance'])
                ->where('overall_status', '=', 'approved')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $modifiedLeaveSummary = collect();
        foreach ($leaveSummary as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);

            // Iterate through each day of the leave
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

                // check for Holiday or Weekend
                if (! $this->checkDate($date) ) {
                    $modifiedLeaveSummary->push([
                        'portal_id' => $leave->user->id,
                        'ingress_id' => $leave->user->ingress_id,
                        'staff_name' => $leave->user->name,
                        'date' => $date->format('d-m-Y'),
                        'leave_type' => $this->dateType($leave, $date->format('Y-m-d')),
                        'duration' => $this->halfDay($leave, $date->format('Y-m-d')),
                        'remark' => $leave->reason,
                    ]);
                }
            }
        }

        $fileName = $this->now->format('d-m-Y') . ' - leave-summary.xlsx';
        $header_style = (new Style())->setFontBold();

        return (new FastExcel($modifiedLeaveSummary))
            ->headerStyle($header_style)
            ->download($fileName);
    }

    public function downloadUserProfile(int $userID)
    {
        $user = User::with(['department', 'personalInformation'])
            ->where('id', $userID)
            ->get();

        $fileName = $user->first()->name .' - Profile (' .$this->now->format('d-m-Y') . ').xlsx';

        return (new FastExcel($user))
            ->download($fileName, function ($user) {
                return [
                    'portal_id' => $user->id,
                    'staff_id' => $user->staff_id,
                    'ingress_id' => $user->ingress_id,
                    'staff_name' => $user->name,
                    'department_name' => $user->department->name,
                    'job_title' => $user->title,
                    'email' => $user->email,
                    'gender' => $user->gender,
                    'date_of_birth' => $user->personalInformation->date_of_birth,
                    'joining_date' => $user->joining_date,
                ];
            });
    }

    public function downloadLeaveBalance()
    {
        $leaveBalances = collect();

        $users = User::with('leaveBalance')->get();

        foreach ($users as $user) {
            $leaveBalances->push([
                'ID' => $user->id,
                'JOINING_DATE' => $user->joining_date,
                'NAME' => $user->name,

                'ANNUAL CARRY FORWARD' => $this->getBalanceDetail($user->leaveBalance, 1, 'CF'),
                'ANNUAL BALANCE' => $this->getBalanceDetail($user->leaveBalance, 1, 'BALANCE'),
                'ANNUAL TOTAL' => $this->getBalanceDetail($user->leaveBalance, 1, 'TOTAL'),
                'ANNUAL TAKEN' => $this->getBalanceDetail($user->leaveBalance, 1, 'TAKEN'),

                'MEDICAL BALANCE' => $this->getBalanceDetail($user->leaveBalance, 2, 'BALANCE'),

                'HOSPITALISATION BALANCE' => $this->getBalanceDetail($user->leaveBalance, 3, 'BALANCE'),

                'UNPAID TAKEN' => $this->getBalanceDetail($user->leaveBalance, 4, 'TAKEN'),

                'EMERGENCY TAKEN' => $this->getBalanceDetail($user->leaveBalance, 5, 'TAKEN'),

                'PATERNITY BALANCE' => $this->getBalanceDetail($user->leaveBalance, 1, 'BALANCE'),

                'MATERNITY BALANCE' => $this->getBalanceDetail($user->leaveBalance, 1, 'BALANCE'),

                'OFFSHORE BALANCE' => $this->getBalanceDetail($user->leaveBalance, 8, 'BALANCE'),

                'REPLACEMENT BALANCE' => $this->getBalanceDetail($user->leaveBalance, 9, 'BALANCE'),

                'COMPASSIONATE TAKEN' => $this->getBalanceDetail($user->leaveBalance, 10, 'TAKEN'),

                'OUT OF OFFICE TAKEN' => $this->getBalanceDetail($user->leaveBalance, 11, 'TAKEN'),
            ]);
        }

        $fileName = $this->now->format('d-m-Y') . ' - leave-balance.xlsx';
        $header_style = (new Style())->setFontBold();

        return (new FastExcel($leaveBalances))
            ->headerStyle($header_style)
            ->download($fileName);
    }

    private function getBalanceDetail($leaveBalances, int $leave_type_id, String $sentData)
    {
        $selectedLeave = null;
        foreach ($leaveBalances as $leaveBalance) {
            if ($leaveBalance->leave_type_id === $leave_type_id) {
                $selectedLeave = $leaveBalance;
            }
        }

        if (is_null($selectedLeave)) {
            return null;
        }

        switch($sentData) {
            case 'CF': return $selectedLeave->carry_forward;
            case 'BALANCE': return $selectedLeave->balance;
            case 'TOTAL': return $selectedLeave->total;
            case 'TAKEN': return $selectedLeave->taken;
            default: return null;
        }
    }

    private function checkDate($date)
    {
        $date = $date->format('d-m-Y');
        $holiday = Holiday::where('date', $date)->first();

        // if the date clash with holiday, returns true;
        if ($holiday) {
            return true;
        }

        // if the date is weekend, returns true;
        return Carbon::parse($date)->isWeekend();
    }

    private function dateType($leaveRequest, $date)
    {
        $leave_type = $leaveRequest->leaveBalance->leave->name;

        if (in_array( $leaveRequest->leaveBalance->leave_type_id, [1,4,5])) {
            if ($date == $leaveRequest->start_date && $leaveRequest->start_date_type != 'full day')
            {
                return ($leaveRequest->start_date_type == 'morning')
                    ? ($leave_type . ' (AM)')
                    :  ($leave_type . ' (PM)');
            }
            else if ($date === $leaveRequest->end_date && $leaveRequest->end_date_type !== 'full day')
            {
                return ($leaveRequest->end_date_type == 'morning')
                    ? ($leave_type . ' (AM)')
                    :  ($leave_type . ' (PM)');
            }
        }

        return $leave_type;
    }

    private function halfDay($leaveRequest, $date)
    {
        if ($date === $leaveRequest->start_date)
        {
            return (in_array($leaveRequest->start_date_type, ['morning', 'evening']))
                ? 0.5
                : 1;
        }
        else if($date === $leaveRequest->end_date) {
            return (in_array($leaveRequest->end_date_type, ['morning', 'evening']))
                ? 0.5
                : 1;
        }

        // if not half day, return 1 which equivalent to full day.
        return 1;
    }
}
