<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->model_name =
            $this->addUnderscoreBeforeCapital(
                explode('\\', $this->model_name)
            );

         ($this->model_name[2] === 'Replacement_Leave') ? $this->model_name[2] = $this->model_name[2].'_Request' : '';

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'color' => $this->color,
            'requester_name' => $this->requester_name,
            'model_name' => $this->model_name[2],
            'model_id' => $this->model_id,
            'status' => $this->status,
            'updated_at' => $this->updated_at->format('d m Y'),
            'hour_diff' => Carbon::parse($this->updated_at)->diffInHours(Carbon::now()),
            'time' => $this->updated_at->diffForHumans(),
        ];
    }

    private function addUnderscoreBeforeCapital($string) {
        return preg_replace('/([a-z])([A-Z])/', '$1_$2', $string);
    }
}
