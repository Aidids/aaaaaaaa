<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveDeductionHistoryResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'remark' => $this->remark,
            'duration' => $this->duration,
            'hrIncharge' => new ApproverResource($this->whenLoaded('hrIncharge')),
            'hr_ic_date' => $this->hr_ic_date,
            'deduct_all' => $this->deduct_all,
            'created_at' => $this->created_at
        ];
    }
}
