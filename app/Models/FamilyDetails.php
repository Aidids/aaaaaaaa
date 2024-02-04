<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'personal_information_id',

        'child_name',
        'child_ic_no',
        'child_cert_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function personalInfo()
    {
        return $this->belongsTo(PersonalInformation::class, 'personal_information_id');
    }
}
