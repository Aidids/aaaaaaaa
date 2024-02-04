<?php

namespace App\Http\Controllers;

use App\Models\TravelAuthorization;
use App\Models\TravelClaim;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EformController extends Controller
{
    public function index(User $user)
    {
        if (Auth::user()->can('update', $user))
        {
            return view('eform.index');
        }

        return abort(401);
    }

    public function apply()
    {
        return view('eform.apply');
    }

    public function approvers()
    {
        if (Auth::user()->can('isApprover', Auth::user()))
        {
            return view('eform.approve');
        }

        return abort(403);
    }

    public function summary()
    {
        $isProjectManager = (Auth::id() === (int)env('TA_PIC_ID'));

        return view('eform.summary', compact('isProjectManager'));
    }

    public function travelAuthorizationShow(TravelAuthorization $travel)
    {
        if (Auth::user()->can('update', $travel->eform->first()))
        {
            return view('eform.show', compact('travel'));
        }

        return abort(403);
    }

    public function travelAuthorizationEdit(TravelAuthorization $travel)
    {
        if (Auth::user()->can('update', $travel->eform->first()))
        {
            return view('eform.show-travel-authorization', compact('travel'));
        }

        return abort(403);
    }

    public function travelExpenseShow(TravelClaim $travel)
    {
        if (Auth::user()->can('authorAndAdmin', $travel->user))
        {
            return view('eform.te-show', compact('travel'));
        }

        return abort(403);
    }

    public function travelExpenseEdit(TravelClaim $travel)
    {
        if (Auth::user()->can('update', $travel->user))
        {
            return view('eform.te-form', compact('travel'));
        }

        return abort(403);
    }

}
