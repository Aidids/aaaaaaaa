<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemOffshoreAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'redeem_offshore_leave_id',
        'name',
        'path',
    ];

    public function offshoreLeave()
    {
        return $this->belongsTo(RedeemOffshoreLeave::class, 'redeem_offshore_leave_id');
    }

    public function store(int $main_model_id, String $path, String $name) {
        return self::create([
            'redeem_offshore_leave_id' => $main_model_id,
            'name' => $name,
            'path' => $path
        ]);
    }
}
