<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index ()
    {
        if (Auth::user()->can('isAdmin', Auth::user()))
        {
            return view('employee.index');
        }

        return abort(403);
    }
}
