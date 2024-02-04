<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function travelForms()
    {
        return $this->hasMany(TravelAuthorization::class);
    }

    public function travelClaims()
    {
        return $this->hasMany(TravelAuthorization::class);
    }
}
