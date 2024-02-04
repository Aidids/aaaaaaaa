<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RedeemOffshoreAttachmentResource extends JsonResource
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
            'redeem_offshore_leave_id' => $this->redeem_offshore_leave_id,
            'name' => $this->name,
            'path' => $this->path,
        ];
    }
}
