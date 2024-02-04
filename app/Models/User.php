<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Models\ActiveDirectory\User as AD;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasApiTokens, HasFactory, Notifiable, AuthenticatesWithLdap;
    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_admin',
        'department_id',
        'approver_id',
        'title',
        'email',
        'gender',
        'contact_no',
        'date_of_birth',
        'joining_date',
        'password',
        'remember_token',
        'ingress_id',
        'staff_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // For Impersonate User Plugin (this used to prevent normal user impersonate another user)
    public function canImpersonate(): bool
    {
        $allowed_id = [2, 13, 186, 24];

        return in_array($this->id, $allowed_id);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approver()
    {
        return $this->belongsTo(Approver::class);
    }

    public function personalInformation()
    {
        return $this->hasOne(PersonalInformation::class);
    }

    public function family()
    {
        return $this->hasMany(FamilyDetails::class);
    }

    public function permAddress()
    {
        return $this->hasOne(PermanentAddress::class);
    }

    public function currAddress()
    {
        return $this->hasOne(CurrentAddress::class);
    }

    public function emergency()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function leaveBalance()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function leaveRequest()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function travelClaims()
    {
        return $this->hasMany(TravelClaim::class);
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public static function isEnabled(int $userID)
    {
        $AD = AD::find(self::find($userID)->dn);

        if ($AD) {
            return $AD->isEnabled();
        }
        return false;
    }

    public static function getDepartment(int $userID)
    {
        return self::with('department')->find($userID)->department;
    }
}
