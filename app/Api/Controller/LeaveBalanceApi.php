<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAnnualLeaveRequest;
use App\Http\Resources\LeaveBalanceResource;
use App\Models\LeaveBalance;
use App\Services\LeaveBalanceService;

class LeaveBalanceApi extends Controller
{
    public function __construct()
    {
        $this->leaveBalanceService = new LeaveBalanceService();
    }

    public function index(int $userId)
    {
        $balance = LeaveBalance::where('user_id',$userId)->get();

        return LeaveBalanceResource::collection($balance);
    }
}
