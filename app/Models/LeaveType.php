<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'gender',
        'pro_rated',
    ];

    protected $casts = [
        'pro_rated' => 'boolean'
    ];

    public function balance()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function entitlement()
    {
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function carryForward()
    {
        return $this->hasMany(LeaveCarryForward::class);
    }

    public static function getReplacementLeaveType()
    {
        return self::where('name', 'Replacement Leave')->first();
    }

    public static function getOffshoreLeaveType()
    {
        return self::where('name', 'Offshore Leave')->first();
    }

    public static function getCompassionateLeaveType()
    {
        return self::where('name', 'Compassionate Leave')->first();
    }

    public static function getOutOfOfficeLeaveType()
    {
        return self::where('name', 'Out Of Office Leave')->first();
    }
}
