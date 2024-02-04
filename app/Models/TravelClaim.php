<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'department_id',
        'custom_approver',
        'approvers_id',
        'approvers_remark',
        'current_approver',
        'status',
        'submission_month',
        'total_allowance',
        'total_transport',
        'total_expense',
        'isDraft',
        'index_page',
        'hr_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'current_approver');
    }

    public function transports()
    {
        return $this->hasMany(Transport::class, 'travel_id');
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class, 'travel_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'travel_id');
    }

    protected static function booted(): void
    {
        static::created(function ($claim) {
            $claim->isDraft = true;
            $claim->save();
        });

        static::deleting(function ($claim){
            // delete all related data from model Allowance
            Allowance::where('travel_id', $claim->id)->delete();

            // delete all related data from model Transport
            Transport::where('travel_id', $claim->id)->delete();

            // delete all related data from model Expense
            Expense::where('travel_id', $claim->id)->delete();
        });
    }
}
