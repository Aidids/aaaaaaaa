<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveDeductionHistoryResource;
use App\Models\LeaveDeductionHistory;
use App\Services\LeaveDeductionHistoryService;
use Illuminate\Http\Request;

class LeaveDeductionHistoryApi extends Controller
{
    public function __construct()
    {
        $this->LeaveDeductionHistoryService = new LeaveDeductionHistoryService();
    }

    public function index()
    {
        $response = LeaveDeductionHistory::with(['user', 'hrIncharge'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return LeaveDeductionHistoryResource::collection($response);
    }

    // pass to service for deduction process
    public function deductLeave(int $hr_id, Request $request)
    {
        // concurrently only can deduct AL
        $data = array_merge($request->toArray(), ['leave_type_id' => 1, 'hr_id' => $hr_id]);

        $response = $this->LeaveDeductionHistoryService->deductLeaveHistory($data);

        return response()->json($response);
    }
}
