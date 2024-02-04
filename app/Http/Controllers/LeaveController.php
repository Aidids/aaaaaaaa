<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function approveLeave()
    {
        if (Auth::user()->can('isApprover', Auth::user()))
        {
            return view('leave.approve-leave');
        }

        return abort(403);
    }

    public function request(User $user)
    {
        if (Auth::user()->can('update', $user))
        {
            return view('leave.request');
        }

        return abort(401);
    }

    public function history(User $user)
    {
        if (Auth::user()->can('update', $user))
        {
            return view('leave.history');
        }

        return abort(401);
    }

    public function summary(User $user)
    {
        if (Auth::user()->can('isAdmin', Auth::user())) {
            return view('leave.summary');
        }

        return abort(403);
    }
}
