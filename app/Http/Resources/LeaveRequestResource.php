<?php

namespace App\Http\Resources;

use App\Models\Approver;
use App\Models\RedeemReplacementLeave;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'requester' => new ApproverResource($this->whenLoaded('user')),
            'leave_balance_id' => $this->leaveBalance->id,
            'balance' => $this->leaveBalance->balance,
            'leave_type_id' => $this->leaveBalance->leave->id,
            'leave_type_name' => $this->leaveBalance->leave->name,
            'start_date' => $this->start_date,
            'start_date_selected' => $this->start_date_type,
            'start_date_type' => $this->start_date_type,
            'end_date' => $this->end_date,
            'end_date_selected' => $this->end_date_type,
            'end_date_type' => $this->end_date_type,
            'duration' => $this->duration,
            'compassionate_type' => $this->compassionate_type,
            'redeem_replacement_selected' => $this->getReplacementDetails($this->id),
            'reason' => $this->reason,
            'attachment' => AttachmentResource::collection($this->whenLoaded('attachment')),
            'first_approver' => new ApproverResource($this->whenLoaded('firstApprover')),
            'first_approver_status' => $this->first_approver_status,
            'first_approver_remark' => $this->first_approver_remark,
            'first_approver_date' => $this->first_approver_date,
            'second_approver' => new ApproverResource($this->whenLoaded('secondApprover')),
            'second_approver_status' => $this->second_approver_status,
            'second_approver_remark' => $this->second_approver_remark,
            'second_approver_date' => $this->second_approver_date,
            'overall_status' => $this->overall_status,
            'leave_created' => $this->created_at->format('Y-m-d'),
            'deduct_type' => $this->deduct_type,
            'calculated' => $this->calculated,
            'hr_note' => $this->hr_note,
            'replacement_coupon' => new RedeemReplacementLeaveResource($this->whenLoaded('replacementCoupon')),
        ];
    }

    private function convertDateType(String $date)
    {
        $defaults = array(
            [
                'name' => 'full day',
                'selected' => false,
            ],
            [
                'name' => 'morning',
                'selected' => false,
            ],
            [
                'name' => 'evening',
                'selected' => false,
            ],
        );

        foreach ($defaults as &$option) {
            if ($option['name'] === $date) {
                $option['selected'] = true;
            }
        }

        return $defaults;
    }

    private function getReplacementDetails(int $leaveRequestID)
    {
       return RedeemReplacementLeave::getSelectedRedemption($leaveRequestID);
    }
}
