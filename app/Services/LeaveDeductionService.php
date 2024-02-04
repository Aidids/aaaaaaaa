<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Log;

class LeaveDeductionService extends Controller
{
    public function annualLeaveDailyCalculateV2(): void
    {
        $affectedRecords = 0;
        Log::info('Leave Deduction Process Start....');

        $leaveRequestRecords = LeaveRequest::with('leaveBalance')
            ->where([
                'overall_status' => 'approved',
                'calculated' => NULL,
            ])->get();

        $leaveRequestRecords->chunk(20)->each(function ($chunks) use (&$affectedRecords){
            foreach ($chunks as $leaveRequest) {

                // leave deduction for FORCE LEAVE
                if ( !$leaveRequest->first_approver_id && !$leaveRequest->second_approver_id
                && $leaveRequest->leaveBalance->leave->id !== 11){
                    Log::info('Unpaid Leave Deduction Success');
                    $this->handleDeductLeave($leaveRequest);
                }
                // deduction flow for UNPAID and EL
                else if (in_array($leaveRequest->leaveBalance->leave->id, [4,5])){
                    $this->handleDeductLeave($leaveRequest);

                    // Record EL leave (for report purpose)
                    ($leaveRequest->leaveBalance->leave->id === 5) && $this->recordEmergencyLeave($leaveRequest);
                }
                // deduction for other leave type
                // to record the taken column for (out of office leave type)
                else {
                    if ($leaveRequest->leaveBalance->balance === 0.0) {
                        $this->handleDeductLeave($leaveRequest);
                    }
                    else {
                        $leaveRequest->leaveBalance->leaveDeduction($leaveRequest->duration);
                    }
                }

                $leaveRequest->update(['calculated' => true]);
                $affectedRecords++;
            }
        });

        Log::info('End....');
        Log::info('Leave Deduction Report: ' . $affectedRecords . ' records of has been updated');
        Log::info('----------------------------------------------------------------------------');
    }

    private function handleDeductLeave(LeaveRequest $leaveRequest)
    {
        $leaveTypeIds = [8, 1, 4];
        $leaveBalances = LeaveBalance::whereIn('leave_type_id', $leaveTypeIds)
            ->where('user_id', $leaveRequest->user_id)
            ->get()
            ->keyBy('leave_type_id');

        $offshoreType = $leaveBalances->get(8);
        $offshoreBalance = $offshoreType->balance;

        $annualType = $leaveBalances->get(1);
        $annualBalance = $annualType->balance;

        $unpaidType = $leaveBalances->get(4);
//        $unpaidBalance = $unpaidType->balance;

        // deduct UNPAID, ONLY IF BOTH OFFSHORE AND ANNUAL BALANCE IS 0
        if( !$offshoreBalance && !$annualBalance )
        {
            $unpaidType->leaveDeduction($leaveRequest->duration);

            $leaveRequest->update(['deduct_type' => 'unpaid']);
            return true;
        }

        // deduct OFFSHORE only
        if($leaveRequest->duration <= $offshoreBalance) {
            $offshoreType->leaveDeduction($leaveRequest->duration);

            $leaveRequest->update(['deduct_type' => 'offshore']);
            return true;
        }
        // deduct OFFSHORE, then -> AL || also applicable for deduct AL only if OFFSHORE 0
        else if ($leaveRequest->duration <= ($offshoreBalance + $annualBalance)) {
            ($offshoreBalance == 0.0 && $annualBalance > 0) ?
                $leaveRequest->update(['deduct_type' => 'annual']) :
                $leaveRequest->update(['deduct_type' => 'offshore & annual']);

            $balanceDiff = ($leaveRequest->duration - $offshoreBalance);

            $offshoreType->leaveDeduction($offshoreBalance);
            $annualType->leaveDeduction($balanceDiff);

            return true;
        }
        else {
            $balanceDiff = ($leaveRequest->duration) - ($offshoreBalance + $annualBalance);

            $offshoreType->leaveDeduction($offshoreBalance);
            $annualType->leaveDeduction($annualBalance);
            $unpaidType->leaveDeduction($balanceDiff);

            if (!$annualBalance) {
                $leaveRequest->update(['deduct_type' => 'offshore & unpaid']);
            }
            else if (!$offshoreBalance) {
                $leaveRequest->update(['deduct_type' => 'annual & unpaid']);
            }
            else {
                $leaveRequest->update(['deduct_type' => 'offshore, annual, & unpaid']);
            }

            return true;
        }
    }

    private function recordEmergencyLeave(LeaveRequest $leaveRequest): void
    {
        // just record emergency leave (affect offshore, annual, and unpaid Leave Type)
        $leaveRequest->leaveBalance->leaveDeduction($leaveRequest->duration); // record EL balance & Taken
    }
}
