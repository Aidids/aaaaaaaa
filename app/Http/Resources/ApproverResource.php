<?php

namespace App\Http\Resources;

use App\Models\Approver;
use Illuminate\Http\Resources\Json\JsonResource;

class ApproverResource extends JsonResource
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
            'department' => $this->department->name ?? 'No department set',
            'position' => $this->title,
            'approver_level' => $this->approver->id ?? null,
        ];
    }
}
