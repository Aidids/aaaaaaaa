<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EFormAttachmentResource extends JsonResource
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
            'path' => $this->path,
            'hr_upload' => (bool)$this->hr_upload,
        ];
    }
}
