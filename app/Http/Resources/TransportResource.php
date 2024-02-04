<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $startLocation = [];
        $endLocation = [];

        $this->exctract($this->start_location, $startLocation);
        $this->exctract($this->end_location, $endLocation);

        return [
            'id' => $this->id,
            'transport_type' => $this->transport_type,
            'date' => ($this->date) ?
                Carbon::parse($this->date)->format('Y-m-d') :
                null,

            'start_location' => $startLocation['location'],
            'start_name' => $startLocation['name'],
            'end_location' => $endLocation['location'],
            'end_name' => $endLocation['name'],
            'total_distance' => $this->total_distance ? (float) $this->total_distance : null,
//            'rate' => (float)  $this->rate,
            'amount' => (float)  $this->amount,
            'remark' => $this->remark,
            'path' => $this->path,
        ];
    }

    private function getTotalRate($distance, $rate)
    {
        return number_format($distance * $rate, 2);
    }

    private function exctract($string, &$location)
    {
        $location['location'] = null;   // "Others"
        $location['name'] = null;   // "Remote location"

        if (! str_contains($string, 'Others'))
        {
            $location['location'] = $string;   // "Others"
            $location['name'] = null;   // "Remote location"
            return;
        }

        $parts = explode("(", $string);
        if (count($parts) == 2)
        {
            $location['location'] = trim($parts[0]);   // "Others"
            $location['name'] = trim(rtrim($parts[1], ")"));   // "Remote location"
        }
    }

}
