<?php

namespace App\Http\Resources;

use App\Models\Approver;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveAddOnResource extends JsonResource
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
            'user_selected' => new ApproverResource($this->whenLoaded('user')),
            'pic_id' => $this->pic_id,
            'person_in_charge' => new ApproverResource($this->whenLoaded('personInCharge')),
            'leave_balance' => new LeaveBalanceResource($this->whenLoaded('leaveBalance')),
            'new_balance' => $this->new_balance,
            'added_qty' => $this->added_qty,
            'remark' => $this->remark,
            'addon_created' => $this->updated_at->format('d M Y'),
        ];
    }
}
