<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdministrationController extends Controller
{
    public function leaveType()
    {
        return view('administration.leave-type');
    }

    public function userRole()
    {
        return view('administration.user-roles');
    }

    public function workShift()
    {
        return view('administration.work-shift');
    }

    public function department()
    {
        return view('administration.department');
    }

    public function approvers()
    {
        if (Auth::user()->can('isAdmin', Auth::user())) {
            return view( 'administration.approvers');
        }
        return abort(403);
    }

    public function holiday()
    {
        return view('administration.holiday');
    }

    public function announcemenet()
    {
        return view('administration.announcemenet');
    }

    public function comingSoon()
    {
        return view('error.coming-soon');
    }

    public function updateAnnualLeave()
    {
        if (Auth::user()->can('isAdmin', Auth::user())) {
            return view('administration.update-annual-leave');
        }

        return abort(401);
    }

    public function deductLeave()
    {
        if (Auth::user()->can('isAdmin', Auth::user())) {
            return view('administration.deduct-leave');
        }

        return abort(401);
    }
}
