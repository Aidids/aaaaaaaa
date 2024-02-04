<?php

namespace App\Http\Resources;

use App\Models\LeaveEntitlement;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveTypeResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'pro_rated' => $this->pro_rated,
            'gender' => [
                'name' => $this->checkGender(),
                'value' => $this->gender,
            ],
            'entitlement' => LeaveRelationshipResource::collection($this->entitlement),
            'carry_forward' => LeaveRelationshipResource::collection($this->carryForward),
        ];
    }

    private function checkGender()
    {
        if ($this->gender == 'f') {
            return 'Female';
        }

        if ($this->gender == 'm') {
            return 'Male';
        }

        return 'All';
    }
}
