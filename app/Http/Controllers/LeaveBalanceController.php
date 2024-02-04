<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveBalanceRequest;
use App\Http\Requests\UpdateLeaveBalanceRequest;
use App\Models\LeaveBalance;

class LeaveBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreLeaveBalanceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeaveBalanceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLeaveBalanceRequest  $request
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeaveBalanceRequest $request, LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveBalance $leaveBalance)
    {
        //
    }
}
