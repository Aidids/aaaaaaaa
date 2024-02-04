<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EFormResource extends JsonResource
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

            'first_approver_status' => $this->first_approver_status,
            'first_approver_remark' => $this->first_approver_remark,
            'first_approver_date' => $this->first_approver_date,

            'second_approver_status' => $this->second_approver_status,
            'second_approver_remark' => $this->second_approver_remark,
            'second_approver_date' => $this->second_approver_date,

            'hr_ic_status' => $this->hr_ic_status,
            'hr_ic_remark' => $this->hr_ic_remark,
            'hr_ic_date' => $this->hr_ic_date,

            'overall_status' => $this->overall_status,

            'requester' => new UserResource($this->whenLoaded('user')),
            'first_approver' => new ApproverResource($this->whenLoaded('firstApprover')),
            'second_approver' => new ApproverResource($this->whenLoaded('secondApprover')),
            'hrIncharge' => new ApproverResource($this->whenLoaded('hrIncharge')),
            'attachments' => EFormAttachmentResource::collection($this->whenLoaded('attachment')),
        ];
    }
}
