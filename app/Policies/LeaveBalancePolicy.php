<?php

namespace App\Policies;

use App\Models\LeaveBalance;
use Illuminate\Auth\Access\HandlesAuthorization;
use LdapRecord\Models\ActiveDirectory\User;

class LeaveBalancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, LeaveBalance $leaveBalance)
    {
        //
    }
}
