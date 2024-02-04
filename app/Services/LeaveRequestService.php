<?php

namespace App\Services;

use App\Enums\LeaveRequestStatus;
use App\Enums\Status;
use App\Http\Traits\ApproverTrait;
use App\Http\Traits\SendEmailTrait;
use App\Mail\LeaveRequestMail;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveRequestAttachment;
use App\Models\LeaveType;
use App\Models\RedeemReplacementLeave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Exception;


class LeaveRequestService
{
    use SendEmailTrait, ApproverTrait;
    public function __construct()
    {
        $this->replacementLeaveService = new RedeemReplacementLeaveService();
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function getAllLeave(int $userId): Collection
    {
        return LeaveBalance::where('user_id', $userId)
            ->select('id', 'user_id', 'leave_type_id', 'balance')
            ->with(['leave' => function ($query) {
                $query->select('id', 'name', 'description');
            }])
            ->get();
    }

    public function applyLeave(int $id, Array $request)
    {
        $userId = ['user_id' => $id];
        $leaveBalance = LeaveBalance::find($request['leave_balance_id']);

        //Compassionate Leave Type is in different API for applying
        if (! $leaveBalance->checkBalance($leaveBalance, $request['duration'])) {
            return 'Balance insufficient, Your balance is : ' . $leaveBalance->balance .' Day\'s';
        }

        /**
         * If ID exists, return edit request method
         */
        if (array_key_exists('id', $request))
        {
            LeaveRequest::find($request['id'])
                ->update(array_merge($userId, Arr::except($request, ['file', 'id'])));

            $this->editAttachment($request['id'], $request);

            $leaveRequest = LeaveRequest::findOrFail($request['id']);
            $this->sendEmail($leaveRequest, $id);

            return $leaveRequest;
        }
        else
        {
            ######################################################
            // Extra Validation step to calculate 'ACTUAL' balance
            $pendingDuration = LeaveRequest::where([
                'leave_balance_id' => $request['leave_balance_id'],
                'overall_status' => Status::pending->value,
            ])->sum('duration');
            $actualBalance = ( $leaveBalance->balance - $pendingDuration );

            if($actualBalance < $request['duration']) {
                Log::info('Insufficient Actual Balance, User ID: ' . $userId['user_id']);
                return 'Balance insufficient, Previously applied leave request is still pending.';
            }
            ######################################################

            $leaveRequest = LeaveRequest::create(array_merge($userId,
                Arr::except($request, 'file')));

            $this->uploadAttachment($leaveRequest->id, $request);
            $this->sendEmail($leaveRequest, $id);


            $replacementLeaveType = LeaveType::getReplacementLeaveType();
            if( ($replacementLeaveType) &&
                ($leaveRequest->leaveBalance->leave_type_id === $replacementLeaveType->id) ) {
                $this->replacementLeaveService->storeLeaveRequestID(
                    replacement_id: $request['replacement_leave_id'],
                    leaveRequest: $leaveRequest,
                );
            }
        }

        return $leaveRequest;
    }

    public function cancelLeave(int $userId, Array $data)
    {
        $leaveRequest = LeaveRequest::with('leaveBalance.leave')
                        ->findOrFail($data['id']);

        $replacementLeaveType = LeaveType::getReplacementLeaveType();
        $replacement = RedeemReplacementLeave::where('leave_request_id', $leaveRequest->id)
            ->first();
        if($leaveRequest->leaveBalance->leave_type_id === $replacementLeaveType->id) {
            // If expired, don't add the balance
            if($replacement->overall_status !== LeaveRequestStatus::expired->value) {
                $replacement->update([
                    'balance_qty' => $replacement->balance_qty + $leaveRequest->duration,
                ]);
            }
        }

        if (! empty($leaveRequest->compassionate_type)) {
            $leaveRequest->update([
                'overall_status' => LeaveRequestStatus::canceled->value,
            ]);

            return;
        }

        if ($leaveRequest->overall_status === 'approved' && $leaveRequest->calculated)
        {
            $LeaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('id', $leaveRequest->leave_balance_id)
                ->firstOrFail();

            // FOR expired voucher of RedeemReplacementLeave
            if ($LeaveBalance->leave_type_id === $replacementLeaveType->id && $replacement->overall_status == LeaveRequestStatus::expired->value) {
                Log::info('User has cancel Leave, but replacement voucher expired,
                ID: '.$leaveRequest->user_id);
            }
            else {
                $LeaveBalance->cancelCalculated($leaveRequest->duration);
            }
        }

        // update overall status to cancel
        $leaveRequest->update([
            'overall_status' => LeaveRequestStatus::canceled->value,
        ]);

        $this->sendCancelEmail($userId, $leaveRequest);
    }

    public function getPendingLeaveRequest(int $approverId)
    {
        $this_year = Carbon::now();

        return LeaveRequest::with(['user', 'firstApprover', 'secondApprover', 'leaveBalance', 'attachment'])
            ->where(function ($query) use ($approverId, $this_year) {
                $query->where('overall_status', '=', LeaveRequestStatus::pending->value)
                ->where('first_approver_id', '=', $approverId)
                ->where('start_date', '>=', $this_year->copy()->firstOfYear()->toDateString())
                ->where('end_date', '<=', $this_year->copy()->endOfYear()->toDateString());
            })->orWhere(function ($query) use ($approverId, $this_year) {
                $query->where('overall_status', '=', LeaveRequestStatus::pending->value)
                ->where('second_approver_id', '=', $approverId)
                ->where('start_date', '>=', $this_year->copy()->firstOfYear()->toDateString())
                ->where('end_date', '<=', $this_year->copy()->endOfYear()->toDateString());
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }

    public function approvePendingLeaveRequest(int $approverId, Array $data): String
    {
        $firstStatus = $this->setLeaveStatus('first_approver', $approverId, $data);
        $secondStatus = $this->setLeaveStatus('second_approver', $approverId, $data);

        $leaveRequest = LeaveRequest::find($data['id']);
        if (in_array($leaveRequest->overall_status, ['approved', 'rejected'])) {
            $leaveType = LeaveBalance::with('leave')
                ->where('id', $leaveRequest->leave_balance_id)
                ->first();

            $this->sendEmailApproveLeave(
                leaveRequest: $leaveRequest,
                userId: $leaveRequest->user_id,
                type: $leaveType->leave->name,
            );
        }

        if ($leaveRequest->overall_status === 'approved' && !empty($leaveRequest->compassionate_type))
        {
            $this->compassionateType($leaveRequest);
        }

        if ($leaveRequest->overall_status === Status::rejected->value) {
            $replacementLeaveType = LeaveType::getReplacementLeaveType();
            $replacement = RedeemReplacementLeave::where('leave_request_id', $leaveRequest->id)
                ->first();

            if($replacement &&
                $leaveRequest->leaveBalance->leave_type_id === $replacementLeaveType->id) {
                // If expired, don't add the balance
                if($replacement->overall_status !== LeaveRequestStatus::expired->value) {
                    $replacement->update([
                        'balance_qty' => $replacement->balance_qty + $leaveRequest->duration,
                    ]);
                }
            }
        }

        if (!is_null($firstStatus))
        {
            return $firstStatus;
        }

        if (!is_null($secondStatus))
        {
            return $secondStatus;
        }

        return 'Something went wrong. Please try again.';
    }

    // To add taken value for user compassionate leave balance
    private function compassionateType(LeaveRequest $leaveRequest)
    {
        $lb = LeaveBalance::find($leaveRequest->leave_balance_id);
        $lb->compassionateTaken($leaveRequest->duration);
    }


    /**
     * Complicated shit approving leave start here
     * Good Luck
     */
    private function setLeaveStatus(String $approverType, int $approverId, Array $data)
    {
        if (!array_key_exists($approverType.'_id', $data))
        {
            return null;
        }

        $leaveRequest = LeaveRequest::where('id', $data['id'])
                        ->where($approverType.'_id', $approverId)
                        ->first();

        if ($leaveRequest->overall_status == LeaveRequestStatus::rejected->value)
        {
            return 'This leave has been rejected by another approver';
        }

        if ($data[$approverType.'_status'] == LeaveRequestStatus::rejected->value)
        {
            $this->updateLeaveRequestModel(
                leaveRequest: $leaveRequest,
                withOverall: true,
                approverType: $approverType,
                approverId: $approverId,
                data: $data
            );

            return 'You have rejected this leave request';
        }

        /**
         * Check 2nd approver ID is null
         */
        if ( $leaveRequest->overall_status == LeaveRequestStatus::pending->value
                && $this->checkDoubleApprovers($leaveRequest, $approverType) )
        {
            $this->updateLeaveRequestModel(
                leaveRequest: $leaveRequest,
                withOverall: true,
                approverType: $approverType,
                approverId: $approverId,
                data: $data
            );

            return 'Leave request status has been successfully approved';
        }

        /**
         * Check 2nd status is null
         */
        if ( $leaveRequest->overall_status == LeaveRequestStatus::pending->value
                && $this->checkDoubleStatus($leaveRequest, $approverType)
            )
        {
            $this->updateLeaveRequestModel(
                leaveRequest: $leaveRequest,
                withOverall: false,
                approverType: $approverType,
                approverId: $approverId,
                data: $data
            );

            return 'You have approve this leave request';
        }

        $this->updateLeaveRequestModel(
            leaveRequest: $leaveRequest,
            withOverall: true,
            approverType: $approverType,
            approverId: $approverId,
            data: $data
        );

        return 'Leave request status has been successfully approved';
    }

    public function getLeaveSummary(Array $data, Bool $is_excel = false)
    {
        $startDate = null; $query = null; $leaveTypeID = null; $department_id = null; $status = ['approved'];

        array_key_exists('month', $data) && $startDate = Carbon::createFromFormat('Y-m-d', $data['month']);
        array_key_exists('query', $data) && $query = $data['query'];
        array_key_exists('type', $data) && $leaveTypeID = $data['type'];
        array_key_exists('department_id', $data) && $department_id = $data['department_id'];
        array_key_exists('status', $data) && $status = $data['status'];

        $summaryData =  LeaveRequest::with(['user', 'firstApprover', 'secondApprover', 'leaveBalance', 'attachment'])
            ->whereHas('user', function ($q) use ($query, $department_id) {
                $q->where('name', 'LIKE', '%'. $query .'%');
                $q->when($department_id, function ($qd) use ($department_id){
                    $qd->where('department_id', $department_id);
                });
            })
            ->whereHas('leaveBalance', function ($q) use ($leaveTypeID) {
                $q->when($leaveTypeID, function ($qlt) use ($leaveTypeID) {
                    $qlt->whereIn('leave_type_id', $leaveTypeID);
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('start_date', '>=', $startDate->toDateString())
                    ->where('end_date', '<=', $startDate->copy()->endOfMonth()->toDateString());
            })
            ->whereIn('overall_status', $status);

        if ($is_excel) {
            return $summaryData
                ->orderBy('start_date', 'desc')
                ->get();
        }

        return $summaryData
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }


    public function addHrNote(int $leaveId, Array $data)
    {
        return LeaveRequest::find($leaveId)->update($data);
    }

    private function checkDoubleApprovers(LeaveRequest $leaveRequest, String $approverType): bool
    {
        $secondApproverId = ($approverType == 'first_approver') ? 'second_approver_id' : 'first_approver_id';

        return is_null($leaveRequest->$secondApproverId);
    }

    private function checkDoubleStatus(LeaveRequest $leaveRequest, String $approverType): bool
    {
        $secondApprover = ($approverType == 'first_approver') ? 'second_approver_status' : 'first_approver_status';

        return ($leaveRequest->$secondApprover == 'pending');
    }

    private function updateLeaveRequestModel(LeaveRequest $leaveRequest, bool $withOverall, String $approverType, int $approverId, Array $data)
    {
        if ($withOverall)
        {
            return $leaveRequest->update([
                    'overall_status' => $data[$approverType.'_status'],
                    $approverType.'_status' => $data[$approverType.'_status'],
                    $approverType.'_remark' => $data[$approverType.'_remark'],
                    $approverType.'_date' => $data[$approverType.'_date'],
                ]);
        }

        return $leaveRequest->update([
                $approverType.'_status' => $data[$approverType.'_status'],
                $approverType.'_remark' => $data[$approverType.'_remark'],
                $approverType.'_date' => $data[$approverType.'_date'],
            ]);
    }

    private function sendEmail(LeaveRequest $leaveRequest, int $user_id): void
    {
        $leaveType = LeaveBalance::with('leave')
            ->where('id', $leaveRequest->leave_balance_id)
            ->first();
        $leaveTypeName = $leaveType->leave->name;
        $this->sendEmailRequestLeave($leaveRequest, $user_id, $leaveTypeName);
    }

    private function uploadAttachment(int $id, Array $data): void
    {
        if (array_key_exists('file', $data) && $data['file']->isValid())
        {
            $file = file_get_contents($data['file']);
            $fileName = $id.'_'.time().'.'.$data['file']->getClientOriginalExtension();
            Storage::disk('leave-request')->put($fileName, $file);

            LeaveRequestAttachment::create([
                'leave_request_id' => $id,
                'path' => $fileName
            ]);
        }
    }

    private function editAttachment(int $leaveRequestId, Array $data): void
    {
        if (array_key_exists('file', $data))
        {
            if (array_key_exists('file_id', $data))
            {
                $attachment = LeaveRequestAttachment::find($data['file_id']);
                Storage::disk('leave-request')->delete($attachment->path);
            }

            $file = file_get_contents($data['file']);
            $fileName = $leaveRequestId.'_'.time().'.'.$data['file']->getClientOriginalExtension();
            Storage::disk('leave-request')->put($fileName, $file);

            LeaveRequestAttachment::updateOrCreate(
                ['leave_request_id' => $leaveRequestId],
                [
                    'path' => $fileName,
                    'leave_request_id' => $leaveRequestId
                ]
            );
        }
    }
}
