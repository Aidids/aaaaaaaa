<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'travel_id',
        'transport_type',
        'date',
        'start_location',
        'end_location',
        'total_distance',
        'amount',
        'remark',
        'path',
    ];

    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_id');
    }

    public static function storeTransport(Array $data)
    {
        return self::create([
            'travel_id' => $data['travel_id'],
            'transport_type' => $data['transport_type'],
            'date' => $data['date'],
            'start_location' => $data['start_location'],
            'end_location' => $data['end_location'],
            'total_distance' => $data['total_distance'],
            'rate' => $data['rate'],
            'remark' => $data['remark'],
        ]);
    }

    protected static function booted()
    {
        static::deleting(function ($mileage) {
            Storage::disk('travel-claim-attachment')
                ->delete('/'. $mileage->travel_id ."/transport/". $mileage->path);
        });
    }
}
