<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedeemLeaveController extends Controller
{
    public function redeemReplacementIndex()
    {
        return view('leave.redeem-index');
    }

    public function redeemReplacementHistory()
    {
        if (Auth::user()->can('update', Auth::user())) {
            return view('leave.redeem-history');
        }

        abort(401);
    }

    public function approveRedeemReplacement()
    {
        if (Auth::user()->can('isApprover', Auth::user())) {
            return view('leave.redeem-approve');
        }

        abort(403);
    }

    public function summaryRedeemReplacement()
    {
        if (Auth::user()->can('isAdmin', Auth::user())) {
            return view('leave.redeem-summary');
        }

        abort(403);
    }
}
