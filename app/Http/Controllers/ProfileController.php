<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function index(User $user)
    {
        if (Auth::user()->can('authorAndAdmin', $user))
        {
            return view('profile.index');
        }

        return abort(403);
    }
}
