<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TravelAuthorizationResource extends JsonResource
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
            'department_name' => $this->department->name ?? null,
            'travel_purpose' => (boolean)$this->travel_purpose,
            'project_name' => $this->project_name,
            'project_location' => $this->project_location,
            'main_office' => $this->main_office,
            'reimbursement' => $this->reimbursement,
            'purpose' => $this->purpose,
            'created_at' => $this->created_at,
            'approvers' => new EFormResource( $this->whenLoaded('eform')->first() ),
            'location' => json_decode($this->location),
        ];
    }
}
