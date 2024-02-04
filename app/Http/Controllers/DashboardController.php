<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function calendar()
    {
        $user = Auth::user();
        if (Auth::user()->can('isAdmin', $user)) {
            $dept = $user->department_id;

            return view('dashboard.calendar', compact('dept'));
        }

        return abort(403);
    }
}
