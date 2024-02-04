<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'department' => $this->department->name ?? 'Not Assigned',
            'approver_id' => $this->whenLoaded('approver')->id ?? null,
            'approver_level' => $this->setApproverText($this->whenLoaded('approver')->id ?? 0),
        ];
    }

    private function setApproverText(int $level)
    {
        if ($level == 0)
        {
            return null;
        }

        return ($level == 1) ? 'first_approver' : 'second_approver';
    }
}
