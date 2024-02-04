<?php

namespace App\Policies;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return Response|bool
     * For Admin Access Rights
     */
    public function viewAny(User $user)
    {
         return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function update(User $user, User $model)
    {
        return $user->id === $model->id;
    }


    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    public function authorAndAdmin(User $user, User $model) {
        return $user->id === $model->id || $user->isAdmin();
    }

    public function isAdmin(User $user)
    {
        return $user->isAdmin();
    }

    public function isApprover(User $user)
    {
        return $user->approver_id !== null;
    }
}
