<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AllowanceGraphResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'allowance_type' => $this->type,
            'total_amount' => (float) $this->total_amount,
        ];
    }
}
