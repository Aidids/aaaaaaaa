<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurrAddressResource extends JsonResource
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
            'module' => 'current_address',
            'user_id' => $this->user_id ?? null,
            'details' => $this->current_address ?? null,
            'city' => $this->current_city ?? null,
            'state' => $this->current_state ?? null,
            'zip' => $this->current_zip ?? null,
            'country' => $this->current_country ?? null,
            'phone' => $this->current_phone ?? null,
        ];
    }
}
