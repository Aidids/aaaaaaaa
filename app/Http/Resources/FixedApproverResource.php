<?php

namespace App\Http\Resources;

use App\Http\Traits\ApproverTrait;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class FixedApproverResource extends JsonResource
{
    use ApproverTrait;
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
            'approvers' => (json_decode($this->approvers_id) !== null)
                ? $this->getApproverDetail(json_decode($this->approvers_id))
                : NULL,
        ];
    }
}
