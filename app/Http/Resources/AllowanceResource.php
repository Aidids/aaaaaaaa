<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $allowance = [];
        $this->extractName($this->allowance_type, $allowance);

        return [
            'id' => $this->id,
            'travel_id' => $this->travel_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'allowance_type' => $allowance['type'],
            'allowance_name' => $allowance['name'],
            'allowance_rate' => (float) $this->allowance_rate,
            'meal_total_hours' => (integer) $this->meal_total_hours,
            'amount' =>  (float) $this->amount,
            'remark' => $this->remark,
            'path' => $this->path
        ];
    }

    private function extractName($string, &$allowance)
    {
        if (! str_contains($string, 'Others'))
        {
            $allowance['type'] = $string;   // "Others"
            $allowance['name'] = null;   // "Remote location"
            return;
        }

        $parts = explode("(", $string);
        if (count($parts) == 2)
        {
            $allowance['type'] = trim($parts[0]);   // "Others"
            $allowance['name'] = trim(rtrim($parts[1], ")"));   // "Remote location"
        }
    }
}
