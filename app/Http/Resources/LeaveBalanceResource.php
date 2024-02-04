<?php

namespace App\Http\Resources;

use App\Models\Approver;
use App\Models\LeaveType;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveBalanceResource extends JsonResource
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
            'name' => $this->leave->name,
            'leave_request_name' => $this->leave->name.$this->hideBalance($this->leave_type_id),
            'user_id' => $this->user_id,
            'leave_type_id' => $this->leave_type_id,
            'leave_type_resource' => new LeaveTypeResource($this->whenLoaded('leave')),
            'proRated' => $this->proRated,
            'entitlement' => $this->entitlement,
            'carry_forward' => $this->carry_forward,
            'balance' => $this->balance,
            'total' => $this->total,
            'taken' => $this->taken,
        ];
    }

    private function hideBalance(int $id)
    {
        if ($id === 4 || $id === 5 || $id === 10 || $id === 11 || $id === 12) {
            return '';
        }

        return ' | Leave Balance: '.$this->balance.' day(s)';
    }
}
