<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveAddOnResource;
use App\Services\LeaveAddOnService;
use Illuminate\Http\Request;

use App\Models\LeaveAddOn;

class LeaveAddOnApi extends Controller
{
    public function __construct()
    {
        $this->leaveAddOnService = new LeaveAddOnService();
    }

    public function index()
    {
        $response = LeaveAddOn::with(['user', 'personInCharge', 'leaveBalance'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return LeaveAddOnResource::collection($response);
    }

    public function addLeave(Request $request)
    {
        $response = $this->leaveAddOnService->addLeaveBalance(
            $request->toArray(),
        );

        return response()->json(['message' => $response]);
    }
}
