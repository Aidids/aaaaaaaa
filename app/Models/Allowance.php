<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_id',
        'path',
        'start_date',
        'end_date',
        'allowance_type',
        'allowance_rate',
        'meal_total_hours',
        'amount',
        'remark',
    ];

    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($allowance) {
            Storage::disk('travel-claim-attachment')
                ->delete('/'.$allowance->travel_id."/allowance/".$allowance->path);
        });
    }
}
