<?php

namespace App\Action;
use App\Services\LeaveBalanceService;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Models\ActiveDirectory\User as AD;
use App\Models\Department;
use App\Models\User;


class LoginAction
{
    public function __construct()
    {
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function handle(User $user): void
    {
        $token = $user->createToken('authToken')->plainTextToken;

        $ldap = AD::find($user->dn);

        if ($ldap) {
            $dept = Department::updateOrCreate(
                ['name' => $ldap->getFirstAttribute('department')],
            );
        }
        else {
            $dept = Department::updateOrCreate(['name' => 'NO DEPARTMENT']);
        }

        $user->update(
            [
                'remember_token' => $token,
                'department_id' => $dept->id,
            ]
        );

        if ($ldap->groups()->contains('app-admin')) {
            $user->update(['is_admin' => true]);
        }

        $this->leaveBalanceService->assignLeaveBalance($user);
    }
}
