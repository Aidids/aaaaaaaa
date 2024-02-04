<?php

namespace App\Services;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApproverTrait;
use App\Http\Traits\AttachmentTrait;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\RedeemOffshoreAttachment;
use App\Models\RedeemOffshoreLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RedeemOffshoreLeaveService extends Controller
{
    use ApproverTrait, AttachmentTrait;

    public function editOffshore(Array $data)
    {
        $redeemOffshore = RedeemOffshoreLeave::findOrFail($data['id'])->update($data);

        // Reset Status of both approvers back to pending
        $this->resetStatus($data['id']);

        return (!$redeemOffshore)
            ? 'Redeem offshore leave edit failed, please contact IT.'
            : 'Redeem offshore leave edit successful' ;
    }

    public function approvalIndexQuery()
    {
        return RedeemOffshoreLeave::with(['user', 'firstApprover', 'secondApprover', 'attachment'])
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

    public function approveOffshore(Array $data)
    {
        $redeemOffshore = RedeemOffshoreLeave::find($data['id']);

        return $this->twoLevelApproval(
            approverID: Auth::id(),
            model: $redeemOffshore,
            data: $data,
        );
    }

    public function summary(Array $data)
    {
        $startDate = null; $query = null; $department_id = null;
        $status=['approved', 'completed', 'expired'];

        array_key_exists('month', $data) && $startDate = Carbon::createFromFormat('Y-m-d', $data['month']);
        array_key_exists('query', $data) && $query = $data['query'];
        array_key_exists('department_id', $data) && $department_id = $data['department_id'];
        array_key_exists('status', $data) && $status = $data['status'];

        return RedeemOffshoreLeave::with(['user', 'firstApprover', 'secondApprover', 'hrIncharge', 'attachment'])
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

    public function finalize(Array $data)
    {
        $redeemOffshore = RedeemOffshoreLeave::find($data['id']);

        $message = $this->hrApproval(
            hrID: Auth::id(),
            model: $redeemOffshore,
            data: $data,
        );
        $redeemOffshore = RedeemOffshoreLeave::find($data['id']);

        if ($redeemOffshore->overall_status == Status::completed->value) {
            $redeemOffshore->update([
                'balance_received' => $data['added_qty'],
            ]);

            // update user balance
            $leaveBalance = LeaveBalance::where([
                'user_id' => $redeemOffshore->user_id,
                'leave_type_id' => LeaveType::getOffshoreLeaveType()->id,
            ])->first();
            $leaveBalance->addBalance($data['added_qty']);
        }

        return $message;
    }

    public function upload(Array $data)
    {
        $this->addMultipleFileV2(
            main_model: RedeemOffshoreLeave::find($data['id']),
            attachment_model: new RedeemOffshoreAttachment(),
            data: $data,
            directory: 'redeem-offshore-leave',
        );

        // Reset Status of both approvers back to pending
        $this->resetStatus($data['id']);
    }

    public function deleteAttachment(Array $data)
    {
        $offshoreAttachment = RedeemOffshoreAttachment::findOrFail($data['attachment_id']);
        $full_path = $offshoreAttachment->redeem_offshore_leave_id . '/' . $offshoreAttachment->path;

        $response = $this->deleteAttachmentV2(
            attachment_model: $offshoreAttachment,
            directory: 'redeem-offshore-leave',
            full_path: $full_path
        );

        // Reset Status of both approvers back to pending
        $this->resetStatus($data['id']);

        return $response;
    }

    private function resetStatus(int $redeem_offshore_id): void
    {
        $redeemOffshore = RedeemOffshoreLeave::find($redeem_offshore_id);

        if ($redeemOffshore->overall_status !== 'pending') {
            return;
        }

        $redeemOffshore->update([
            'first_approver_status' => (is_null($redeemOffshore->first_approver_id)) ? NULL : 'pending',
            'second_approver_status' => (is_null($redeemOffshore->second_approver_id)) ? NULL : 'pending',
        ]);
    }
}
