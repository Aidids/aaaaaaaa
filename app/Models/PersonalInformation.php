<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',

        'user_id',
        'nickname',
        'date_of_birth',
        'place_of_birth',

        'ic_no',
        'passport_no',
        'phone_no',

        'race',
        'religion',
        'nationality',

        'marital_status',
        'spouse_name',
        'spouse_ic_no',
        'spouse_work',
        'marriage_cert_path',

        'epf_no',
        'socso_no',
        'income_tax_no',
        'bank_name',
        'bank_acc_no',
        'bank_acc_type',

        'educations',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function family()
    {
        return $this->hasMany(FamilyDetails::class);
    }
}
