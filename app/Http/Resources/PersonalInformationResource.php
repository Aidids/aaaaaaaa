<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonalInformationResource extends JsonResource
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
            'nickname' => $this->nickname,
            'date_of_birth' => $this->date_of_birth,
            'place_of_birth' => $this->place_of_birth,

            'ic_no' => $this->ic_no,
            'passport_no' => $this->passport_no,
            'phone_no' => $this->phone_no,

            'race' => $this->race,
            'religion' => $this->religion,
            'nationality' => $this->nationality,

            'marital_status' => ($this->marital_status) ? 'married' : 'single',
            'spouse_name' => $this->spouse_name,
            'spouse_ic_no' => $this->spouse_ic_no,
            'spouse_working' => ($this->spouse_work) ? 'yes' : 'no',
            'marriage_cert_path' => $this->marriage_cert_path,

            'epf_no' => $this->epf_no,
            'socso_no' => $this->socso_no,
            'income_tax_no' => $this->income_tax_no,
            'bank_name' => $this->bank_name,
            'bank_acc_no' => $this->bank_acc_no,
            'bank_acc_type' => (int)$this->bank_acc_type,
            'educations' => ($this->educations) ?
                EducationResource::collection(json_decode($this->educations)) : [],

            'marriage_cert' => $this->marriage_cert_path,

            'children' => FamilyDetailResource::collection($this->whenLoaded('family')) ?? NULL,
        ];
    }
}
