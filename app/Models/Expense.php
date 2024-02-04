<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_id',
        'description', // $request['others'] .'('. $request['others_name'] .')'; Others(minum)
        'account_code',
        'total_hours',
        'amount',
        'remark',
        'path',
    ];

    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_id');
    }

    protected static function booted(): void
    {
        static::deleting(function ($expense) {
            Storage::disk('travel-claim-attachment')
                ->delete('/'. $expense->travel_id . "/expense/". $expense->path);
        });
    }
}
