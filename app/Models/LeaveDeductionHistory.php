<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDeductionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'remark',
        'duration',
        'hr_ic_id',
        'hr_ic_date',
        'deduct_all',
        'leave_request_id',
    ];

    protected $casts = [
        'deduct_all' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hrIncharge()
    {
        return $this->belongsTo(User::class, 'hr_ic_id');
    }
}
