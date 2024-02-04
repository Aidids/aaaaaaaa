<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'details',
        'city',
        'state',
        'zip',
        'country',
        'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
