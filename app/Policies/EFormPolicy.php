<?php

namespace App\Policies;

use App\Models\EForm;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class EFormPolicy
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
     * @param  \App\Models\EForm  $eForm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, EForm $eForm)
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
     * @param  \App\Models\EForm  $eForm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, EForm $eForm)
    {
        return $user->id === $eForm->user_id || $user->id === (int)env('TA_PIC_ID') ||$user->isAdmin() ;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\EForm  $eForm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, EForm $eForm)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\EForm  $eForm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, EForm $eForm)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \LdapRecord\Models\ActiveDirectory\User  $user
     * @param  \App\Models\EForm  $eForm
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, EForm $eForm)
    {
        //
    }
}
