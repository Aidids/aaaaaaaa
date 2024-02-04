<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'eformable_id',
        'eformable_type',
        'user_id',

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
        'file',
    ];

    public function eformable()
    {
        return $this->morphTo();
    }

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
        return $this->hasMany(EFormAttachment::class);
    }

    public static function createEForm(Array $data, Model $model)
    {
        return self::create([
            'eformable_id' => $model->id,
            'eformable_type' => get_class($model),
            'user_id' => $data['user_id'],
            'first_approver_id' => $data['first_approver_id'],
            'first_approver_status' => $data['first_approver_status'],
            'second_approver_id' => $data['second_approver_id'],
            'second_approver_status' => $data['second_approver_status'],
            'overall_status' => $data['overall_status'],
        ]);
    }

    public static function getEFormDetails(Model $model)
    {
        return self::where([
            'eformable_id' => $model->id,
            'eformable_type' => get_class($model),
        ])->first();
    }
}
