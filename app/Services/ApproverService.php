<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;


class ApproverService
{
    public function assignApprover(Array $data): User
    {
        $user = User::findOrFail($data['user_id']);
        $user->update($data);

        return $user;
    }
}
