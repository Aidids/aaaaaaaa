<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransportGraphResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'transport_type' => $this->transport_type,
            'total_amount' => (float) $this->total_amount,
        ];
    }
}
