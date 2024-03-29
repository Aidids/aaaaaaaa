<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'id' => $this->id ?? null,
            'details' => $this->details ?? null,
            'city' => $this->city ?? null,
            'state' => $this->state ?? null,
            'zip' => $this->zip ?? null,
            'country' => $this->country ?? null,
            'phone' => $this->phone ?? null,
        ];
    }
}
