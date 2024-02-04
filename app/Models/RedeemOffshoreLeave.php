<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemOffshoreLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',

        'start_date',
        'end_date',
        'remark',

        'balance_received', //this column value will be stored, after fully approved by approvers

        'first_approver_id',
        'first_approver_status',
        'first_approver_remark',
        'first_approver_date',

        'second_approver_id',
        'second_approver_status',
        'second_approver_remark',
        'second_approver_date',

        'hr_ic_id',
        'hr_ic_status',
        'hr_ic_remark',
        'hr_ic_date',

        'overall_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function firstApprover()
    {
        return $this->belongsTo(User::class, 'first_approver_id');
    }

    public function secondApprover()
    {
        return $this->belongsTo(User::class, 'second_approver_id');
    }

    public function hrIncharge()
    {
        return $this->belongsTo(User::class, 'hr_ic_id');
    }

    public function attachment()
    {
        return $this->hasMany(RedeemOffshoreAttachment::class);
    }

    public static function storeRedeemOffshore(Array $data)
    {
        return self::create([
            'user_id' => $data['user_id'],

            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'remark' => $data['remark'] ?? NULL,

            'first_approver_id' => $data['first_approver_id'] ?? NULL,
            'first_approver_status' => $data['first_approver_status'] ?? NULL,
            'second_approver_id' => $data['second_approver_id'] ?? NULL,
            'second_approver_status' => $data['second_approver_status'] ?? NULL,

            'overall_status' => Status::pending->value,
        ]);
    }
}
