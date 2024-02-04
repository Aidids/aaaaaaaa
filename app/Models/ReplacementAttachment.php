<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplacementAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'redeem_replacement_leave_id',
        'name',
        'path',
    ];

    public function replacementLeave()
    {
        return $this->belongsTo(RedeemReplacementLeave::class, 'replacement_leave_id');
    }
}
