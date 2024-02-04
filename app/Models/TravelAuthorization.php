<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'department_id',
        'travel_purpose',
        'project_name',
        'project_location',
        'main_office',
        'reimbursement',
        'location',
        'purpose',
    ];

    protected $casts = [
        'travel_purpose' => 'boolean',
    ];


    public function eform()
    {
        return $this->morphMany(EForm::class, 'eformable');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public static function storeTravel(Array $data)
    {
        return self::create([
            'department_id' => $data['department_id'],

            // if 1 sent email to lin HR, if 0 sent email to marissa
            'travel_purpose' => ($data['travel_purpose'] === 'true') ? 1 : 0,

            // if travel purpose is true, both below are not required
            'project_name' => $data['project_name'] ?? null,
            'project_location' => $data['project_location'] ?? null,

            'main_office' => $data['main_office'],
            'reimbursement' => $data['reimbursement'],
            'location' => $data['location'],
            'purpose' => $data['purpose'],
        ]);
    }
}
