<?php

namespace App\Http\Resources;

use App\Models\FamilyDetails;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyDetailResource extends JsonResource
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
            'child_name' => $this->child_name,
            'child_ic' => $this->child_ic_no,
            'birth_cert_path' => $this->child_cert_path,
        ];
    }
}
