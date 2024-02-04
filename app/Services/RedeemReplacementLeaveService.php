<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApproverTrait;
use App\Http\Traits\AttachmentTrait;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\RedeemReplacementLeave;
use App\Models\ReplacementAttachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RedeemReplacementLeaveService extends Controller
{
    use ApproverTrait, AttachmentTrait;

    public function __construct()
    {
        $this->now = Carbon::now()->timezone('Asia/Kuala_Lumpur');
    }

    public function applyReplacement(int $userID, Array $data)
    {
        $data['user_id'] = $userID;

        $redeemReplacementLeave = RedeemReplacementLeave::create($data);

        if (array_key_exists('files', $data))
        {
            $this->uploadReplacementAttachment(
                data: array_merge(
                    ['redeem_replacement_leave_id' => $redeemReplacementLeave->id],
                    ['files' => $data['files']]
                ),
                replacementLeave: $redeemReplacementLeave,
            );
        }

        if ($redeemReplacementLeave) {
            return [
                'message' => 'Replacement Leave request successfully sent. We have sent an email to your respective approvers.',
                'status' => 200
            ];
        }

        return [
            'message' => 'Replacement Leave request Fail',
            'status' => 500,
        ];
    }

    public function editReplacement(Array $data)
    {
        $replacementLeave = RedeemReplacementLeave::findOrFail($data['id'])->update($data);

        if($replacementLeave) {
            return 'Redeem replacement leave edit successful';
        }

        return 'Edit Redeem replacement leave unsuccessful';
    }

    public function approvalIndexQuery()
    {
        return RedeemReplacementLeave::with(['user', 'firstApprover', 'secondApprover', 'attachment'])
            ->whereHas('firstApprover', function ($query) {
                $query->where([
                    'first_approver_id' => Auth::id(),
                    'first_approver_status' => Status::pending->value,
                ]);
            })
            ->orWhereHas('secondApprover', function ($query) {
                $query->whereNull('first_approver_id');
                $query->where([
                    'second_approver_id' => Auth::id(),
                    'second_approver_status' => Status::pending->value,
                ]);
            })
            ->orWhereHas('secondApprover', function ($query) {
                $query->where('first_approver_status', Status::approved->value);
                $query->where([
                    'second_approver_id' => Auth::id(),
                    'second_approver_status' => Status::pending->value,
                ]);
            })
            ->where('overall_status', Status::pending->value)
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
    }

    public function approveReplacement(Array $data)
    {
        $replacementLeave = RedeemReplacementLeave::find($data['id']);

        return $this->twoLevelApproval(
            approverID: Auth::id(),
            model: $replacementLeave,
            data: $data,
        );
    }

    public function getReplacementSummary(Array $data)
    {
        $startDate = null; $query = null; $department_id = null;
        $status=['approved', 'completed', 'expired'];

        array_key_exists('month', $data) && $startDate = Carbon::createFromFormat('Y-m-d', $data['month']);
        array_key_exists('query', $data) && $query = $data['query'];
        array_key_exists('department_id', $data) && $department_id = $data['department_id'];
        array_key_exists('status', $data) && $status = $data['status'];

        return RedeemReplacementLeave::with(['user', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment'])
            ->whereHas('user', function ($q) use ($query, $department_id) {
                $q->where('name', 'LIKE', "%{$query}%");
                $q->when($department_id, function ($qd) use ($department_id) {
                    $qd->where('department_id', $department_id);
                });
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('start_date', '>=', $startDate->toDateString())
                    ->where('end_date', '<=', $startDate->copy()->endOfMonth()->toDateString());
            })
            ->whereIn('overall_status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function finalizeReplacement(int $hrID, Array $data)
    {
        // for HR approved or rejected
        // after result  -> add balance to user replacement leave
        $replacementLeave = RedeemReplacementLeave::find($data['id']);

        $message = $this->hrApproval($hrID, $replacementLeave, $data);

        $replacementLeave = RedeemReplacementLeave::find($replacementLeave->id);

        if ($replacementLeave->overall_status === 'completed'){
            $leaveBalance = $this->getLeaveBalance($replacementLeave->user_id);
            $leaveBalance->addBalance($data['added_qty']);

            $expiredDate = Carbon::parse($replacementLeave->hr_ic_date)->addDays(7);
            $replacementLeave->update([
                'expired_date' => $expiredDate,
                'added_qty' => $data['added_qty'],
                'balance_qty' => $data['added_qty'],
            ]);
        }

        return $message;
    }

    public function uploadReplacementAttachment (Array $data, RedeemReplacementLeave $replacementLeave = null)
    {
        $replacementAttachment = new ReplacementAttachment();

        $data['redeem_replacement_leave_id'] = array_key_exists('id', $data)
            ? $data['id']
            : $replacementLeave->id;

        $response = $this->addMultipleAttachment(
            data: $data,
            storage_name: 'redeem-replacement-leave',
            main_model_id: 'redeem_replacement_leave_id',
            model: $replacementAttachment
        );

        if($response) {
            $this->resetStatus($data['redeem_replacement_leave_id']);
        }

        return $response;
    }

    public function deleteReplacementAttachment(Array $data)
    {
        $replacementAttachment = ReplacementAttachment::find($data['attachment_id']);
        $data['redeem_replacement_leave_id'] = $data['id'];


        $response = $this->deleteAttachment($data['attachment_id'], $replacementAttachment, 'redeem-replacement-leave');

        if($response) {
            $this->resetStatus($data['redeem_replacement_leave_id']);
        }

        return $response;
    }

    public function storeLeaveRequestID(int $replacement_id, LeaveRequest $leaveRequest): void
    {
        $replacement  = RedeemReplacementLeave::find($replacement_id);

        $replacement->update([
            'leave_request_id' => $leaveRequest->id,
            'balance_qty' => ($replacement->added_qty - $leaveRequest->duration),
        ]);
    }

    public function getLeaveBalance(int $userID)
    {
        $replacementLeaveType = LeaveType::getReplacementLeaveType();

        return LeaveBalance::where([
            'leave_type_id' => $replacementLeaveType->id,
            'user_id' => $userID,
        ])->first();
    }

    private function resetStatus(int $replacement_leave_id):void
    {
        $replacementLeave = RedeemReplacementLeave::find($replacement_leave_id);

        if ($replacementLeave->overall_status !== 'pending') {
            return;
        }

        $replacementLeave->update([
            'first_approver_status' => (is_null($replacementLeave->first_approver_id)) ? NULL : 'pending',
            'second_approver_status' => (is_null($replacementLeave->second_approver_id)) ? NULL : 'pending',
        ]);
    }
}
