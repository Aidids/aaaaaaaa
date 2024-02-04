<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RedeemOffshoreLeaveResource extends JsonResource
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
            'request_created' => $this->created_at->format('d M Y'),
            'user_id' => $this->user_id,
            'start_date' => Carbon::parse($this->start_date)->format('d M Y'),
            'end_date' => Carbon::parse($this->end_date)->format('d M Y'),
            'remark' => $this->remark,
            'balance_received' => $this->balance_received,

            'first_approver_status' => $this->first_approver_status,
            'first_approver_remark' => $this->first_approver_remark,
            'first_approver_date' => Carbon::parse($this->first_approver_date)->format('d M Y'),

            'second_approver_status' => $this->second_approver_status,
            'second_approver_remark' => $this->second_approver_remark,
            'second_approver_date' => Carbon::parse($this->second_approver_date)->format('d M Y'),

            'hr_ic_status' => $this->hr_ic_status,
            'hr_ic_remark' => $this->hr_ic_remark,
            'hr_ic_date' => Carbon::parse($this->hr_ic_date)->format('d M Y'),

            'overall_status' => $this->overall_status,

            'requester' => new ApproverResource($this->whenLoaded('user')),
            'first_approver' => new ApproverResource($this->whenLoaded('firstApprover')),
            'second_approver' => new ApproverResource($this->whenLoaded('secondApprover')),
            'hr_incharge' => new ApproverResource($this->whenLoaded('hrIncharge')),
            'attachment' => RedeemOffshoreAttachmentResource::collection($this->whenLoaded('attachment')),
        ];
    }
}
