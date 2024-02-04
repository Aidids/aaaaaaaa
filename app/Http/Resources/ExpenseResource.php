<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expense = [];
        $this->extractName($this->description, $expense);

        return [
            'id' => $this->id,
            'description' => $expense['type'],
            'description_name' => $expense['name'],
            'account_code' => $this->account_code,
            'total_hours' => (float) $this->total_hours,
            'amount' => $this->amount ? (float) $this->amount : null,
            'remark' => $this->remark,
            'path' => $this->path,
        ];
    }

    private function extractName($string, &$expense)
    {
        if (! str_contains($string, 'Others'))
        {
            $expense['type'] = $string;   // "Others"
            $expense['name'] = null;   // "data inside ( )"
            return;
        }

        $parts = explode("(", $string);
        if (count($parts) == 2)
        {
            $expense['type'] = trim($parts[0]);   // "Others"
            $expense['name'] = trim( rtrim($parts[1], ")") );   // "data inside ( )"
        }
    }
}
