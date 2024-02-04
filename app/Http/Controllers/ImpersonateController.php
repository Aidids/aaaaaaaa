<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function index($id)
    {
        $checkAuthority = User::find(Auth::id());
        // Block Other user that are not authorized to impersonate
        if (! $checkAuthority->canImpersonate()) {
            abort(403);
        }

        $user = User::find($id);
        if ($user) {
            Auth::user()->impersonate($user); // need more research on how to use this
        }

        // redirect to profile page, to show that the impersonate is success
        return redirect('/profile-settings/' . $user->id);
    }

    public function impersonate_leave()
    {
        Auth::user()->leaveImpersonation();

        // redirect back to employee list page
        return redirect('/employee');
    }
}
