<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RedeemReplacementLeaveResource extends JsonResource
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
            'start_date' => Carbon::parse($this->start_date)->format('d M Y'),
            'end_date' => Carbon::parse($this->end_date)->format('d M Y'),
            'duration' => $this->duration,
            'remark' => $this->remark,
            'first_approver' => new ApproverResource($this->whenLoaded('firstApprover')),
            'first_approver_id' => $this->first_approver_id,
            'first_approver_status' => $this->first_approver_status,
            'first_approver_remark' => $this->first_approver_remark,
            'first_approver_date' => Carbon::parse($this->first_approver_date)->format('d M Y'),
            'second_approver' => new ApproverResource($this->whenLoaded('secondApprover')),
            'second_approver_id' => $this->second_approver_id,
            'second_approver_status' => $this->second_approver_status,
            'second_approver_remark' => $this->second_approver_remark,
            'second_approver_date' => Carbon::parse($this->second_approver_date)->format('d M Y'),
            'hr_incharge' => new ApproverResource($this->whenLoaded('hrIncharge')),
            'hr_ic_status' => $this->hr_ic_status,
            'hr_ic_date' => Carbon::parse($this->hr_ic_date)->format('d M Y'),
            'hr_ic_remark' => $this->hr_ic_remark,
            'added_qty' => $this->added_qty,
            'balance_qty' => $this->balance_qty,
            'overall_status' => $this->overall_status,
            'expired_date' => Carbon::parse($this->expired_date)->format('d M Y'),
            'replacement_options' => 'Balance: ' . $this->balance_qty . ' Day\'s | Expired Date: ' . Carbon::parse($this->expired_date)->format('d M Y'),
            'request_created' => $this->created_at->format('d M Y'),
            'attachment' => ReplacementAttachmentResource::collection($this->whenLoaded('attachment')),
        ];
    }
}
