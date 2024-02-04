<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'isAdmin' => $this->is_admin,
            'title' => $this->title,
            'email' => $this->email,
            'gender' => $this->gender,
            'contact_no' => $this->contact_no,
            'date_of_birth' => $this->date_of_birth,
            'department' => $this->department->name ?? null,
            'department_id' => $this->department->id ?? null,
            'joining_date' => $this->joining_date,
            'staff_id' => $this->staff_id,
            'ingress_id' => $this->ingress_id,
            'approver_level' => $this->setApproverText($this->approver_id ?? 0),
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
